<?php

namespace common\helpers;

use Exception;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\FileNotFoundException;
use Throwable;
use Yii;
use yii\helpers\FileHelper as BaseFileHelper;

class FileHelper extends BaseFileHelper
{

    /**
     * Check file exist
     * @param string $url
     * @return bool
     */
    public static function fileFromUrlExists($url): bool
    {
        $headers = get_headers($url);
        return (substr($headers[0], 9, 3) === '200');
    }

    /**
     * Delete file from s3 by path
     * @param string $path
     * @return bool
     */
    public static function deleteFileByPath($path): bool
    {
        try {
            return Yii::$app->fs->delete($path);
        } catch (FileNotFoundException $ex) {
            LogHelper::warning(
                'Failed to remove file (already removed)',
                'file',
                ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]
            );
            return true; //file already not exist
        } catch (Throwable $ex) {
            LogHelper::error(
                'Failed to remove file',
                'file',
                ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]
            );
            return false;
        }
    }

    /**
     * Delete file from s3 by path
     * @param string $path
     * @return bool
     */
    public static function deleteDirByPath($path): bool
    {
        try {
            return Yii::$app->fs->deleteDir($path);
        } catch (FileNotFoundException $ex) {
            LogHelper::warning(
                'Failed to remove directory (already removed)',
                'file',
                ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]
            );
            return true; //directory already not exist
        } catch (Throwable $ex) {
            LogHelper::error(
                'Failed to remove directory',
                'file',
                ['error' => $ex->getMessage(), 'trace' => $ex->getTraceAsString()]
            );
            return false;
        }
    }

    /**
     * Upload file to s3 and return path
     * @param string $file File path
     * @param string $extention file extention
     * @return string relative s3 path
     * @throws Exception
     */
    public static function uploadFileToPath(string $file, string $relativePath, ?string $extention = null): string
    {
        $stream = fopen($file, 'r+');
        if ($stream === false) {
            throw new Exception('Failed to save m3u8 file to s3');
        }
        try {
            $sourceExtension = self::prepareSourceExtension($extention, $file);
            $path = self::genUniqPath($relativePath, $sourceExtension);
            Yii::$app->fs->writeStream($path, $stream);
            return $path;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * Return full url by path
     * @param string $path
     * @return string
     */
    public static function getUrlByPath(string $path): string
    {
        /** @var AbstractAdapter $adapter */
        $adapter = Yii::$app->fs->getAdapter();
        return $adapter->getClient()->getObjectUrl(Yii::$app->fs->bucket, $adapter->applyPathPrefix($path));
    }

    /**
     * Get url without prefix (store it in variable, get url and restore original prefix)
     * @param string $path
     * @return string
     */
    public static function getUrlWIthoutPrefix($path)
    {
        /** @var AbstractAdapter $adapter */
        $adapter = Yii::$app->fs->getAdapter();
        $prefix = Yii::$app->fs->prefix; //save
        $adapter->setPathPrefix(""); //for vonage archive - no preffix (other folder)
        $url = $adapter->getClient()->getObjectUrl(Yii::$app->fs->bucket, $adapter->applyPathPrefix($path));
        $adapter->setPathPrefix($prefix); //restore
        return $url;
    }

    /**
     * Generate unique path in storage
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
     * Get file extention. If file do not have it -> extract from mime type
     * @param string|null $extension
     * @param string $sourcePath
     * @return string|null
     */
    public static function prepareSourceExtension(?string $extension, string $sourcePath)
    {
        $sourceExtension = $extension ?? pathinfo($sourcePath, PATHINFO_EXTENSION);
        if (!$sourceExtension) {
            $mimeType = self::getMimeType($sourcePath);
            $extensions = self::getExtensionsByMimeType($mimeType);
            if (!empty($extensions)) {
                $sourceExtension = end($extensions);
            }
        }
        return $sourceExtension;
    }
}
