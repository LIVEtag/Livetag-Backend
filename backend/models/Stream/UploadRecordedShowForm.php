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

    /** @var string */
    public $name;

    /** @var int */
    public $shopId;

    /** @var UploadedFile */
    public $file;

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
            'file' => Yii::t('app', 'File'),
            'directUrl' => Yii::t('app', 'Direct URL link'),
            'productIds' => Yii::t('app', 'Products'),
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
            $currentTime = time();
            $this->streamSession->startedAt = $currentTime;
            $this->streamSession->announcedAt = $currentTime;
            $this->streamSession->stoppedAt = $currentTime + StreamSession::DEFAULT_DURATION;
            if (!$this->streamSession->save()) {
                $this->addErrors($this->streamSession->getErrors());
                $transaction->rollBack();
                return false;
            }
            if ($this->isLink()) {
                $this->setFileFromUrl();
            }
            if (!$this->uploadFile()) {
                $attribute = $this->isLink() ? 'directUrl' : 'file';
                $errorsList = Yii::createObject(ErrorListInterface::class);
                $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_INVALID));
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
    public function setFileFromUrl(): bool
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

        $this->file = new UploadedFile([
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
    private function uploadFile(): bool
    {
        $archive = new StreamSessionArchive();
        $archive->setFile($this->file);
        if (!$archive->saveFile()) {
            return false;
        }
        $archive->streamSessionId = $this->streamSession->id;
        if (!$archive->save()) {
            return false;
        }

        return true;
    }
}
