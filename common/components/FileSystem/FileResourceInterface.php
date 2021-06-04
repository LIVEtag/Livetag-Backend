<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem;

use yii\web\UploadedFile;

/**
 * Entity has file and path with relative url to store it
 */
interface FileResourceInterface
{

    /**
     * Get name of field, that used for storing file (path)
     * @return string
     */
    public static function getPathFieldName(): string;

    /**
     * Get name of field, that used for loading file (path)
     * @return string
     */
    public static function getFileFieldName(): string;

    /**
     * @param UploadedFile $value
     */
    public function setFile(UploadedFile $value);

    /**
     * Load file from url and populate file property
     * @param string $url
     */
    public function setFileFromUrl(string $url);

    /**
     * Get relative path for file store
     * @return string
     */
    public function getRelativePath(): string;

    /**
     * Save file resource
     * @return bool
     */
    public function saveFile(): bool;

    /**
     * Get url to file resource
     * @return string
     */
    public function getUrl(): ?string;

    /**
     * Delete file resource
     * @return bool
     */
    public function deleteFile(): bool;
}
