<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\format;

use common\components\FileSystem\format\formatter\FileFormatterInterface;
use yii\web\UploadedFile;

interface FileFormatInterface
{

    /**
     * Return array with key as format name and class as file transformer
     * @return FileFormatterInterface[]
     */
    public function getFormatters(): array;

    /**
     * Get name of field, that used for storing formatted data (as array)
     * @return string
     */
    public static function getFormattedFieldName(): string;

    /**
     * @return array|null
     */
    public function getFormattedUrls(): ?array;

    /**
     * @param string $name
     * @return string|null
     */
    public function getFormattedUrlByName(string $name): ?string;

    /**
     * @return void
     */
    public function removeFormattedItems(): void;

    /**
     * Generate allowed formats.
     * @param string $path S3 path of original file
     * @param UploadedFile $file
     * @return bool
     */
    public function createFormat(string $path, UploadedFile $file): bool;
}
