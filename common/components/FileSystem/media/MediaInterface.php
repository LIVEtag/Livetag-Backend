<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\media;

use common\components\FileSystem\FileResourceInterface;

/**
 * Interface Media
 * @package common\components\FileSystem\media
 *
 * @see MediaTrait
 */
interface MediaInterface extends FileResourceInterface
{
    /**
     * Available Image Mime Types
     */
    const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
    ];

    /**
     * Available Video Mime Types
     */
    const VIDEO_MIME_TYPES = [
        'video/mp4',
    ];

    /**
     * mimetypes by content type
     */
    const MIME_TYPES = [
        MediaTypeEnum::TYPE_IMAGE => self::IMAGE_MIME_TYPES,
        MediaTypeEnum::TYPE_VIDEO => self::VIDEO_MIME_TYPES
    ];

    /**
     * Return array of MediaTypeEnum constatns, that match content type
     * For example
     * `
     *  return [MediaTypeEnum::TYPE_IMAGE];
     * `
     * @return array
     */
    public static function getMediaTypes(): array;

    /**
     * @return array
     */
    public static function getMimeTypes(): array;

    /**
     * Get name of field, that used detecting media type (required)
     * @return string
     */
    public static function getTypeFieldName(): string;

    /**
     * Get name of field, that used for saving original file name (optional)
     * It is assumed that if there is no this field in the model - the method will return NULL
     * @return string|null
     */
    public static function getOriginNameFieldName(): ?string;

    /**
     * Get name of field, that used for saving original file size (optional)
     * It is assumed that if there is no this field in the model - the method will return NULL
     * @return string|null
     */
    public static function getSizeFieldName(): ?string;

    /**
     * @return bool
     */
    public function isVideo(): bool;

    /**
     * @return bool
     */
    public function isImage(): bool;

    /**
     * @return bool
     */
    public function isOther(): bool;
}
