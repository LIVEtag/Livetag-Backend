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
use Throwable;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class UploadRecordedShowForm
 * @package backend\models\Stream
 */
class UploadRecordedShowForm extends Model
{
    /**
     * To add url link to the video
     */
    const TYPE_LINK = 'link';

    /**
     * To upload video
     */
    const TYPE_UPLOAD = 'upload';

    const TYPES = [
        self::TYPE_LINK,
        self::TYPE_UPLOAD,
    ];

    const RESPONSE_CODE_SUCCESS = 200;

    /** @var string */
    public $type;

    /** @var string */
    public $name;

    /** @var int */
    public $shopId;

    /** @var UploadedFile */
    public $file;

    /** @var UploadedFile */
    private $fileFromUrl;

    /** @var string|null */
    public $directUrl;

    /** @var array */
    public $productIds;

    /** @var StreamSession */
    public $streamSession;

    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->streamSession = new StreamSession(['status' => StreamSession::STATUS_STOPPED]);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'productIds', 'shopId'], 'required'],
            ['name', 'string', 'max' => StreamSession::MAX_NAME_LENGTH],
            ['type', 'in', 'range' => self::TYPES],
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
            ['file', 'required', 'when' => function () {
                return $this->isUpload();
            }],
            [
                'file',
                'file',
                'mimeTypes' => StreamSessionArchive::getMimeTypes(),
                'maxSize' => Yii::$app->params['maxUploadVideoSize'],
            ],
        ];
    }

    /**
     * @param $attribute
     * @throws \yii\base\InvalidConfigException
     */
    public function validateFileFromUrl($attribute)
    {
        // phpcs:disable
        $ch = curl_init($this->$attribute);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
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
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_TOO_BIG)
                ->setParams([
                    'file' => basename($this->$attribute),
                    'formattedLimit' => Yii::$app->params['maxUploadVideoSize'],
                ]));
        }
        // phpcs:enable
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name of livestream'),
            'file' => Yii::t('app', 'File'),
            'directUrl' => Yii::t('app', 'Direct URL link'),
            'productIds' => Yii::t('app', 'Products'),
        ];
    }


    /**
     * @return bool
     */
    public function isLink()
    {
        return $this->type === self::TYPE_LINK;
    }

    /**
     * @return bool
     */
    public function isUpload()
    {
        return $this->type === self::TYPE_UPLOAD;
    }

    /**
     * @return bool
     * @throws Throwable
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->streamSession->setAttributes($this->getAttributes());
            //TODO Change period
            $currentTime = time();
            $this->streamSession->startedAt = $currentTime;
            $this->streamSession->announcedAt = $currentTime;
            $this->streamSession->stoppedAt = $currentTime + StreamSession::DURATION_180;
            if (!$this->streamSession->save()) {
                $this->addErrors($this->streamSession->getErrors());
                $transaction->rollBack();
                return false;
            }

            $errorsList = Yii::createObject(ErrorListInterface::class);
            if ($this->isLink() && (!$this->saveFileLocallyFromUrl() || !$this->uploadFile($this->fileFromUrl))) {
                $this->addError('directUrl', $errorsList->createErrorMessage(ErrorList::FILE_INVALID));
                $transaction->rollBack();
                return false;
            }

            if ($this->isUpload() && !$this->uploadFile($this->file)) {
                $this->addError('file', $errorsList->createErrorMessage(ErrorList::FILE_INVALID));
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
     * @return bool
     */
    private function saveFileLocallyFromUrl()
    {
        // phpcs:disable
        $fileName = basename($this->directUrl);
        $filePath = $this->getFileDir() . $fileName;
        $fileContent = file_get_contents($this->directUrl);
        if (!$fileContent) {
            return false;
        }

        if (!file_put_contents($filePath, $fileContent)) {
            return false;
        }

        $this->fileFromUrl = new UploadedFile([
            'name' => $fileName,
            'tempName' => $filePath,
            'size' => filesize($filePath),
            'type' => mime_content_type($filePath),
        ]);
        // phpcs:enable
        return true;
    }

    /**
     * @return string
     */
    private function getFileDir()
    {
        return Yii::getAlias('@webroot/uploads/');
    }

    /**
     * @return bool
     */
    private function removeLocalFile()
    {
        if (!$this->isLink()) {
            return false;
        }
        // phpcs:disable
        return unlink($this->getFileDir() . basename($this->directUrl));
        // phpcs:enable
    }

    /**
     * Create archive and save file to s3
     * @param UploadedFile $uploadedFile
     * @return bool
     * @throws Throwable
     */
    private function uploadFile(UploadedFile $uploadedFile): bool
    {
        $archive = new StreamSessionArchive();
        $archive->setFile($uploadedFile);
        if (!$archive->saveFile()) {
            return false;
        }
        $archive->streamSessionId = $this->streamSession->id;
        if (!$archive->save()) {
            return false;
        }

        $this->removeLocalFile();
        return true;
    }
}
