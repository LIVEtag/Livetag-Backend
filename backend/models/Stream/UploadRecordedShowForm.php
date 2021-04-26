<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use common\components\validation\ErrorList;
use common\components\validation\ErrorListInterface;
use common\models\Stream\StreamSessionArchive;
use common\models\Stream\StreamSessionCover;
use Throwable;
use Yii;
use yii\web\UploadedFile;

/**
 * Class UploadRecordedShowForm
 * @package backend\models\Stream
 */
class UploadRecordedShowForm extends SaveAnnouncementForm
{
    /**
     * To add url link to the video
     */
    const TYPE_LINK = 'link';

    /**
     * To upload video from device storage
     */
    const TYPE_UPLOAD = 'upload';

    const UPLOAD_TYPES = [
        self::TYPE_LINK,
        self::TYPE_UPLOAD,
    ];

    const RESPONSE_CODE_SUCCESS = 200;

    /** @var string */
    public $uploadType;

    /** @var UploadedFile */
    public $videoFile;

    /** @var string|null */
    public $directUrl;

    /**
     * @param StreamSession|null $streamSession
     * @param array $config
     */
    public function __construct(StreamSession $streamSession = null, $config = array())
    {
        $this->streamSession = $streamSession ?: new StreamSession(['status' => StreamSession::STATUS_STOPPED]);
        $this->streamSession->scenario = StreamSession::SCENARIO_UPLOAD_SHOW;
        parent::__construct($this->streamSession, $config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'shopId'], 'required'],
            ['name', 'string', 'max' => StreamSession::MAX_NAME_LENGTH],
            ['uploadType', 'in', 'range' => self::UPLOAD_TYPES],
            [
                'productIds', //validate in main model. select2 do not return null on empty select
                'filter',
                'filter' => function ($value) {
                    return $value == '' ? null : $value;
                }
            ],
            ['directUrl', 'required', 'when' => function () {
                return $this->isLink();
            }],
            ['directUrl', 'url', 'defaultScheme' => 'https'],
            ['directUrl', 'validateFileFromUrl'],
            ['videoFile', 'required', 'when' => function () {
                return $this->isUpload();
            }],
            [
                'videoFile',
                'file',
                'mimeTypes' => StreamSessionArchive::getMimeTypes(),
                'maxSize' => Yii::$app->params['maxUploadVideoSize'],
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
     * @param $attribute
     * @throws \yii\base\InvalidConfigException
     */
    public function validateFileFromUrl($attribute)
    {
        $ch = curl_init($this->$attribute);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
        curl_exec($ch);
        // phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
        $info = curl_getinfo($ch);
        curl_close($ch);

        $errorsList = Yii::createObject(ErrorListInterface::class);
        if (!isset($info['http_code']) || $info['http_code'] !== self::RESPONSE_CODE_SUCCESS) {
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::URL_INVALID)
                ->setParams(['attribute' => $attribute]));
        }

        if (!isset($info['content_type']) || !in_array($info['content_type'], StreamSessionArchive::getMimeTypes())) {
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_WRONG_MIME_TYPE)
                ->setParams(['mimeTypes' => implode(', ', StreamSessionArchive::getMimeTypes())]));
        }
        if (!isset($info['download_content_length'])
            || ($info['download_content_length'] > Yii::$app->params['maxUploadVideoSize'])) {
            // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_TOO_BIG)
                ->setParams([
                    'file' => basename($this->$attribute),
                    'formattedLimit' => Yii::$app->params['maxUploadVideoSize'],
                ]));
            // phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name of livestream'),
            'videoFile' => Yii::t('app', 'File'),
            'directUrl' => Yii::t('app', 'Direct URL link'),
            'productIds' => Yii::t('app', 'Products'),
            'file' => Yii::t('app', 'Photo (cover image)'),
        ];
    }


    /**
     * @return bool
     */
    public function isLink(): bool
    {
        return $this->uploadType === self::TYPE_LINK;
    }

    /**
     * @return bool
     */
    public function isUpload(): bool
    {
        return $this->uploadType === self::TYPE_UPLOAD;
    }

    /**
     * @return bool
     * @throws Throwable
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
                $transaction->rollBack();
                return false;
            }
            if ($this->isLink()) {
                $this->setFileFromUrl();
            }
            if (!$this->uploadVideoFile()) {
                $attribute = $this->isLink() ? 'directUrl' : 'videoFile';
                $errorsList = Yii::createObject(ErrorListInterface::class);
                $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_INVALID));
                $transaction->rollBack();
                return false;
            }

            if (!$this->uploadCover()) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            $this->streamSession->archive->sendToQueue();//note - send outside transaction
            return true;
        } catch (Throwable $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * @return bool
     */
    protected function setFileFromUrl(): bool
    {
        // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
        $fileName = basename($this->directUrl);
        $tempFile = tmpfile();

        $metaData = stream_get_meta_data($tempFile);
        if (!isset($metaData['uri'])) {
            return false;
        }

        $fp = fopen($metaData['uri'], 'w+b');
        $ch = curl_init($this->directUrl);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        // phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
        curl_close($ch);

        $this->videoFile = new UploadedFile([
            'name' => $fileName,
            'tempName' => $metaData['uri'],
            // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
            'size' => filesize($metaData['uri']),
            'type' => mime_content_type($metaData['uri']),
            // phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions
            'tempResource' => $tempFile,
        ]);
        return true;
    }

    /**
     * Create archive and save file to s3
     * @return bool
     * @throws Throwable
     */
    protected function uploadVideoFile(): bool
    {
        $archive = new StreamSessionArchive(['streamSessionId' => $this->streamSession->id]);
        $ffprobe = Yii::$app->params['ffprobe'];
        // Returns video duration string that contains seconds like '15.021667'
        // phpcs:disable PHPCS_SecurityAudit.BadFunctions
        $duration = exec(
            $ffprobe
            . ' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "'
            . $this->videoFile->tempName
            . '"'
        );
        // phpcs:enable PHPCS_SecurityAudit.BadFunctions
        $archive->duration = (int)$duration;
        $archive->setFile($this->videoFile);
        if (!$archive->saveFile()) {
            return false;
        }
        if (!$archive->save()) {
            return false;
        }
        $this->streamSession->populateRelation(StreamSession::REL_ARCHIVE, $archive);
        return true;
    }
}
