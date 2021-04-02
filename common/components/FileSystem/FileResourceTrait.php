<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem;

use common\helpers\LogHelper;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\FileNotFoundException;
use Throwable;
use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Trait FileResourceTrait, implements FileResourceInterface.
 * phpcs:disable PHPCS_SecurityAudit.BadFunctions
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

        $sourceExtension = self::prepareSourceExtension($file->getExtension(), $file->tempName);
        $path = self::genUniqPath($this->getRelativePath(), $sourceExtension);
        try {
            Yii::$app->fs->writeStream($path, $stream);
            $this->setPath($path); //set successfully saved path to model
            return true;
        } catch (Throwable $ex) {
            $this->addError(self::getFileFieldName(), 'Failed to upload file:' . $ex->getMessage());
            LogHelper::error('Failed to upload file', 'file', LogHelper::extraForException($this, $ex));
            return false;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
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
        /** @var AbstractAdapter $adapter */
        $adapter = Yii::$app->fs->getAdapter();
        return $adapter->getClient()->getObjectUrl(Yii::$app->fs->bucket, $adapter->applyPathPrefix($this->getPath()));
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
        if (!self::deleteFileByPath($this->getPath())) {
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

    /**
     * Delete file from s3 by path (todo: move to helper or other class)
     * @param string $path
     * @return bool
     */
    public static function deleteFileByPath($path): bool
    {
        try {
            return Yii::$app->fs->delete($path);
        } catch (FileNotFoundException $ex) {
            LogHelper::warning('Failed to remove file (already removed)', 'file', ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]);
            return true; //file already not exist
        } catch (Throwable $ex) {
            LogHelper::error('Failed to remove file', 'file', ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]);
            return false;
        }
    }

    /**
     * Generate unique path in storage  (todo: move to helper or other class)
     * @param string $relativePath
     * @param string $extension
     * @return string
     */
    public static function genUniqPath(string $relativePath, string $extension)
    {
        $uniqId = str_replace('.', '', uniqid('', true));
        return "{$relativePath}/{$uniqId}.{$extension}";
    }

    /**
     * Get file extention. If file do not have it -> extract from mime type (todo: move to helper or other class)
     * @param string|null $extension
     * @param string $sourcePath
     * @return string|null
     */
    public static function prepareSourceExtension(?string $extension, $sourcePath)
    {
        $sourceExtension = $extension ?? pathinfo($sourcePath, PATHINFO_EXTENSION);
        if (!$sourceExtension) {
            $mimeType = FileHelper::getMimeType($sourcePath);
            $extensions = FileHelper::getExtensionsByMimeType($mimeType);
            if (!empty($extensions)) {
                $sourceExtension = end($extensions);
            }
        }
        return $sourceExtension;
    }
}
