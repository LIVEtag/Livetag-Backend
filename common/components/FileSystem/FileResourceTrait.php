<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem;

use common\helpers\FileHelper;
use common\helpers\LogHelper;
use Throwable;
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
