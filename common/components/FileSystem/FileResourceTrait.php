<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem;

use common\components\FileSystem\format\FileFormatInterface;
use common\helpers\FileHelper;
use common\helpers\LogHelper;
use Throwable;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Trait FileResourceTrait, implements FileResourceInterface.
 * @see FileResourceInterface
 */
trait FileResourceTrait
{
    /**
     * Get name of field, that used for storing file (path)
     * @return string
     */
    public static function getPathFieldName(): string
    {
        return 'path';
    }

    /**
     * Get name of field, that used for storing file (UploadedFile)
     * @return string
     */
    public static function getFileFieldName(): string
    {
        return 'file';
    }

    /**
     * Some kind of checks before file save
     * @return bool
     */
    public function beforeSaveFile(): bool
    {
        if (!$this instanceof Model) {
            throw new InvalidConfigException('Entity should extend yii\base\Model');
        }
        $file = $this->getFile();
        if (!$file || !$file instanceof UploadedFile) {
            $this->addError(self::getFileFieldName(), 'Please specify a valid file to save.');
            return false;
        }
        return true;
    }

    /**
     * Save file
     * @return bool
     */
    public function saveFile(): bool
    {
        if (!$this->beforeSaveFile()) {
            return false;
        }
        $file = $this->getFile();

        $stream = fopen($file->tempName, 'r+');
        if ($stream === false) {
            $this->addError(self::getFileFieldName(), 'Can\'t get content from resource');
            return false;
        }

        try {
            $path = FileHelper::uploadFileToPath($file->tempName, $this->getRelativePath());
            $this->setPath($path); //set successfully saved path to model
            //create formatted versions if entity implements FileFormatInterface
            if ($this instanceof FileFormatInterface) {
                if (!$this->createFormat($path, $file)) {
                    $this->addError(self::getFileFieldName(), $this->getFirstError(self::getFormattedFieldName()));
                    return false;
                }
            }
            return true;
        } catch (Throwable $ex) {
            $this->addError(self::getFileFieldName(), 'Failed to upload file:' . $ex->getMessage());
            LogHelper::error('Failed to upload file', 'file', LogHelper::extraForException($this, $ex));
            return false;
        }
    }

    /**
     * Get url fo resource
     * @return string
     */
    public function getUrl(): ?string
    {
        if (!$this->getPath()) {
            return null;
        }
        return FileHelper::getUrlByPath($this->getPath());
    }

    /**
     * @return bool
     */
    public function deleteFile(): bool
    {
        if (!$this->getPath()) {
            $this->addError(self::getPathFieldName(), 'Failed to remove file');
            return false;
        }
        if (!FileHelper::deleteFileByPath($this->getPath())) {
            $this->addError(self::getPathFieldName(), 'Failed to remove file');
            return false;
        }
        //remove formatted versions if entity implements FileFormatInterface
        if ($this instanceof FileFormatInterface) {
            $this->removeFormattedItems();
        }
        return true;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        $field = self::getFileFieldName();
        return $this->$field;
    }

    /**
     * @param UploadedFile $value
     */
    public function setFile(UploadedFile $value)
    {
        $field = self::getFileFieldName();
        $this->$field = $value;
    }

    /**
     * Load file from url and populate file property
     * @param string $url
     */
    public function setFileFromUrl(string $url)
    {
        $fileName = basename($url);
        $tempFile = tmpfile();

        $metaData = stream_get_meta_data($tempFile);
        if (!isset($metaData['uri'])) {
            throw new Exception('Incorrect File');
        }

        $fp = fopen($metaData['uri'], 'w+b');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);

        $file = new UploadedFile([
            'name' => $fileName,
            'tempName' => $metaData['uri'],
            'size' => filesize($metaData['uri']),
            'type' => mime_content_type($metaData['uri']),
            'tempResource' => $tempFile,
        ]);
        $this->setFile($file);
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        $pathField = self::getPathFieldName();
        return $this->$pathField;
    }

    /**
     * @param string $value
     */
    public function setPath($value)
    {
        $pathField = self::getPathFieldName();
        $this->$pathField = $value;
    }
}
