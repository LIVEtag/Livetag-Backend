<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use common\models\Stream\StreamSessionCover;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Description of SaveAnnouncementForm
 *
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
class SaveAnnouncementForm extends Model
{
    /**
     * Format of input date
     */
    const DATETIME_FORMAT = 'php:Y-m-d H:i';

    /**
     * @var int
     */
    public $shopId;

    /**
     * @var string
     */
    public $name;

    /**
     * Display format of announcedAt
     * @var string
     */
    public $announcedAtDatetime;

    /**
     *  @var int - timestamp
     */
    public $announcedAt;

    /**
     * @var int
     */
    public $duration = StreamSession::DEFAULT_DURATION;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @var array|null
     */
    public $productIds;

    /**
     * @var bool
     */
    public $internalCart = StreamSession::INTERNAL_CART_FALSE;

    /**
     * @var StreamSession
     */
    public $streamSession;

    /**
     * @param StreamSession $streamSession
     * @param array $config
     */
    public function __construct(StreamSession $streamSession = null, $config = array())
    {
        if ($streamSession) {
            $this->setAttributes($streamSession->getAttributes());
            $this->productIds = $streamSession->getProductIds();
            if ($streamSession->announcedAt) {
                $this->announcedAtDatetime = Yii::$app->formatter->asDatetime($streamSession->announcedAt, self::DATETIME_FORMAT);
            }
        }
        $this->streamSession = $streamSession ?: new StreamSession(['status' => StreamSession::STATUS_NEW]);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'shopId', 'announcedAtDatetime', 'duration', 'internalCart'], 'required'],
            [['internalCart'], 'boolean'],
            [
                ['productIds'], //validate in main model. select2 do not return null on empty select
                'filter',
                'filter' => function ($value) {
                    return $value == '' ? null : $value;
                }
            ],
            ['name', 'string', 'max' => StreamSession::MAX_NAME_LENGTH],
            [
                'announcedAtDatetime',
                'datetime',
                'format' => self::DATETIME_FORMAT,
                'timeZone' => Yii::$app->formatter->timeZone,
                'timestampAttribute' => 'announcedAt',
                'skipOnEmpty' => false
            ],
            [
                'file',
                'file',
                'mimeTypes' => StreamSessionCover::getMimeTypes(),
                'maxSize' => Yii::$app->params['maxUploadImageSize'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Name of livestream',
            'announcedAtDatetime' => 'Start At',
            'duration' => 'Maximum duration of this show',
            'productIds' => 'Products',
            'internalCart' => 'Product details view',
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->streamSession->setAttributes($this->getAttributes());
            if (!$this->streamSession->save()) {
                $this->addErrors($this->streamSession->getErrors());
                if ($this->streamSession->hasErrors('announcedAt')) {
                    $this->addError('announcedAtDatetime', $this->streamSession->getFirstError('announcedAt'));
                }
                $transaction->rollBack();
                return false;
            }
            if (!$this->uploadCover()) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            return true;
        } catch (Throwable $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }


    /**
     * Upload file to s3
     * @return boolean
     */
    protected function uploadCover()
    {
        if ($this->file instanceof UploadedFile && !$this->uploadFile($this->file)) {
            return false;
        }
        return true;
    }

    /**
     * Upload single file and store it as media
     * @param UploadedFile $uploadedFile
     * @return bool
     * @throws Throwable
     */
    protected function uploadFile(UploadedFile $uploadedFile): bool
    {
        $oldCover = $this->streamSession->streamSessionCover ?? null;

        $media = new StreamSessionCover();
        $media->setFile($uploadedFile);
        if (!$media->saveFile()) {
            $this->addErrors($media->getErrors());
            return false;
        }
        $media->streamSessionId = $this->streamSession->id;
        if (!$media->save()) {
            $this->addErrors($media->getErrors());
            return false;
        }
        $this->streamSession->populateRelation(StreamSession::REL_STREAM_SESSION_COVER, $media);
        // delete old cover(if exist)
        if ($oldCover) {
            $oldCover->delete();
        }

        return true;
    }
}
