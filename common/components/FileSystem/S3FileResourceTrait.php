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
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Trait S3FileResourceTrait, implements FileResourceInterface.
 * phpcs:disable PHPCS_SecurityAudit.BadFunctions
 * @see FileResourceInterface
 */
trait S3FileResourceTrait
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
     * @return bool
     */
    public function saveFile(): bool
    {
        $fileField = self::getFileFieldName();
        $file = $this->$fileField;

        if (!$file instanceof UploadedFile) {
            throw new InvalidConfigException('Incorrect file type');
        }


        $stream = fopen($file->tempName, 'r+');
        if ($stream === false) {
            $this->addError(self::getFileFieldName(), 'Can\'t get content from resource');
            return false;
        }

        $sourceExtension = $this->prepareSourceExtension($file->getExtension(), $file->tempName);
        $path = $this->genUniqPath($this->getRelativePath(), $sourceExtension);
        try {
            Yii::$app->fs->writeStream($path, $stream);
            $this->setPath($path);
        } catch (Throwable $ex) {
            $this->addError(self::getFileFieldName(), 'Failed to upload file:' . $ex->getMessage());
            LogHelper::error('Failed to upload file', 'file', LogHelper::extraForException($this, $ex));
            return false;
        }
        if (is_resource($stream)) {
            fclose($stream);
        }
        return true;
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
            return false;
        }
        try {
            return Yii::$app->fs->delete($this->getPath());
        } catch (FileNotFoundException $ex) {
            LogHelper::warning('Failed to remove file', 'file', LogHelper::extraForException($this, $ex));
            return true;
        } catch (Throwable $ex) {
            $this->addError(self::getPathFieldName(), 'Failed to remove file:' . $ex->getMessage());
            LogHelper::error('Failed to remove file', 'file', LogHelper::extraForException($this, $ex));
            return false;
        }
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
     * @param string $path
     */
    public function setPath($path)
    {
        $pathField = self::getPathFieldName();
        $this->$pathField = $path;
    }

    /**
     * Generate unique path in storage
     * @param string $relativePath
     * @param string $extension
     * @return string
     */
    protected function genUniqPath(string $relativePath, string $extension)
    {
        $uniqId = str_replace('.', '', uniqid('', true));
        return "{$relativePath}/{$uniqId}.{$extension}";
    }

    /**
     * @param string|null $extension
     * @param $sourcePath
     * @return mixed|string|string[]|null
     * @throws InvalidConfigException
     */
    protected function prepareSourceExtension(?string $extension, $sourcePath)
    {
        $sourceExtension = $extension ?? pathinfo($sourcePath, PATHINFO_EXTENSION);
        $mimeType = null;
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
