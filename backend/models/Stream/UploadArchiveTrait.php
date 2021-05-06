<?php
/*
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
use yii\base\InvalidConfigException;
use yii\web\UploadedFile;

/**
 * Logic for archive upload
 * Trait UploadArchiveTrait, implements UploadArchiveInterface.
 *
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
trait UploadArchiveTrait
{
    /** @var string */
    public $uploadType;

    /** @var UploadedFile */
    public $videoFile;

    /** @var string|null */
    public $directUrl;

    /**
     * @return array
     */
    public function getArchiveValidationRules(): array
    {
        return [
            [UploadArchiveInterface::FIELD_UPLOAD_TYPE, 'in', 'range' => UploadArchiveInterface::UPLOAD_TYPES],
            [
                UploadArchiveInterface::FIELD_DIRECT_URL,
                'required',
                'when' => function () {
                    return $this->isLink();
                }
            ],
            [UploadArchiveInterface::FIELD_DIRECT_URL, 'url', 'defaultScheme' => 'https'],
            [UploadArchiveInterface::FIELD_DIRECT_URL, 'validateFileFromUrl'],
            [UploadArchiveInterface::FIELD_VIDEO_FILE,
                'required',
                'when' => function () {
                    return $this->isUpload();
                }
            ],
            [
                UploadArchiveInterface::FIELD_VIDEO_FILE,
                'file',
                'mimeTypes' => StreamSessionArchive::getMimeTypes(),
                'maxSize' => Yii::$app->params['maxUploadVideoSize'],
            ]
        ];
    }

    /**
     * @param $attribute
     * @throws InvalidConfigException
     */
    public function validateFileFromUrl($attribute)
    {
        $ch = curl_init($this->$attribute);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $errorsList = Yii::createObject(ErrorListInterface::class);
        if (!isset($info['http_code']) || $info['http_code'] !== UploadArchiveInterface::RESPONSE_CODE_SUCCESS) {
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::URL_INVALID)
                    ->setParams(['attribute' => $attribute]));
        }

        if (!isset($info['content_type']) || !in_array($info['content_type'], StreamSessionArchive::getMimeTypes())) {
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_WRONG_MIME_TYPE)
                    ->setParams(['mimeTypes' => implode(', ', StreamSessionArchive::getMimeTypes())]));
        }
        if (!isset($info['download_content_length']) || ($info['download_content_length'] > Yii::$app->params['maxUploadVideoSize'])) {
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_TOO_BIG)
                ->setParams([
                        'file' => basename($this->$attribute),
                        'formattedLimit' => Yii::$app->params['maxUploadVideoSize'],
                    ]));
        }
    }

    /**
     * @return bool
     */
    public function isLink(): bool
    {
        return $this->uploadType === UploadArchiveInterface::TYPE_LINK;
    }

    /**
     * @return bool
     */
    public function isUpload(): bool
    {
        return $this->uploadType === UploadArchiveInterface::TYPE_UPLOAD;
    }

    /**
     * Save archive from file or url     *
     * @return bool
     */
    public function saveArchive(): bool
    {
        //if upload file by url - load it, validate and populate `videoFile`
        if ($this->isLink()) {
            if (!$this->setFileFromUrl()) {
                $this->addError(UploadArchiveInterface::FIELD_DIRECT_URL, 'Failed to upload file from url');
                return false;
            }
        }
        //create archive from `videoFile`
        if (!$this->uploadVideoFile()) {
            $attribute = $this->isLink() ? UploadArchiveInterface::FIELD_DIRECT_URL : UploadArchiveInterface::FIELD_VIDEO_FILE;
            $errorsList = Yii::createObject(ErrorListInterface::class);
            $this->addError($attribute, $errorsList->createErrorMessage(ErrorList::FILE_INVALID));
            return false;
        }
        return true;
    }

    /**
     * Load file from directUrl and populate videoFile
     * @return bool
     */
    protected function setFileFromUrl(): bool
    {
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
        curl_close($ch);

        $this->videoFile = new UploadedFile([
            'name' => $fileName,
            'tempName' => $metaData['uri'],
            'size' => filesize($metaData['uri']),
            'type' => mime_content_type($metaData['uri']),
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
        $ffprobe = Yii::$app->params['ffprobe'];
        // Returns video duration string that contains seconds like '15.021667'
        $duration = exec(
            $ffprobe
            . ' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "'
            . $this->videoFile->tempName
            . '"'
        );
        // Can be 'N/A' for image, '' for wrong paths or for 0 secs video
        if (!$duration || !(int) $duration) {
            return false;
        }

        $oldArchive = $this->getStreamSession()->archive;

        $archive = new StreamSessionArchive(['streamSessionId' => $this->streamSession->id]);
        $archive->duration = (int) $duration;
        $archive->setFile($this->videoFile);
        if (!$archive->saveFile()) {
            return false;
        }
        if (!$archive->save()) {
            return false;
        }
        $this->getStreamSession()->populateRelation(StreamSession::REL_ARCHIVE, $archive);

        // delete old archive(if exist)
        if ($oldArchive) {
            $oldArchive->delete();
        }

        return true;
    }
}
