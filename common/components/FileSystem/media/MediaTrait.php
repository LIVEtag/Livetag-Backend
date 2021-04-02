<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\media;

use common\components\FileSystem\FileResourceTrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Trait Media - implements Media interface
 * @package common\components\FileSystem\media
 * @see MediaInterface
 */
trait MediaTrait
{
    use FileResourceTrait {
        FileResourceTrait::beforeSaveFile as parentBeforeSaveFile;
    }

    /**
     * Some kind of checks before file save
     * @return bool
     */
    public function beforeSaveFile(): bool
    {
        if (!$this->parentBeforeSaveFile()) {
            return false;
        }

        $file = $this->getFile();
        //check that uploaded file is valid type
        $type = self::getAttachmentType($file->tempName);
        if (!in_array($type, self::getMediaTypes())) {
            $this->addError(
                self::getFileFieldName(),
                Yii::t('app', 'Only files with these types are allowed: {types}.', ['types' => implode(', ', self::getMediaTypes())])
            );
            return false;
        }
        $this->setType(self::getAttachmentType($file->tempName));
        $this->setOriginName($file->name);
        $this->setSize($file->size);
        return true;
    }

    /**
     * Get name of field, that used detecting media type (required)
     * @return string
     */
    public static function getTypeFieldName(): string
    {
        return 'type';
    }

    /**
     * Get name of field, that used for saving original file name (optional)
     * It is assumed that if there is no this field in the model - the method will return NULL
     * @return string|null
     */
    public static function getOriginNameFieldName(): ?string
    {
        return 'originName';
    }

    /**
     * Get name of field, that used for saving original file size (optional)
     * It is assumed that if there is no this field in the model - the method will return NULL
     * @return string|null
     */
    public static function getSizeFieldName(): ?string
    {
        return 'size';
    }

    /**
     * @return bool
     */
    public function isVideo(): bool
    {
        return $this->isType(MediaTypeEnum::TYPE_VIDEO);
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->isType(MediaTypeEnum::TYPE_IMAGE);
    }

    /**
     * @return bool
     */
    public function isOther(): bool
    {
        return $this->isType(MediaTypeEnum::TYPE_OTHER);
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function isType(string $type): bool
    {
        return $this->getType() === $type;
    }

    /**
     * Get attachement type in MediaTypeEnum dictionary
     * @param string $path
     * @return string
     */
    public static function getAttachmentType($path)
    {
        $type = explode('/', FileHelper::getMimeType($path))[0];
        if (in_array($type, [MediaTypeEnum::TYPE_IMAGE, MediaTypeEnum::TYPE_VIDEO])) {
            return $type;
        }
        return MediaTypeEnum::TYPE_OTHER;
    }

    /**
     * @return array
     */
    public static function getMimeTypes(): array
    {
        $mimeTypes = [];
        foreach (self::getMediaTypes() as $mediaType) {
            $mimeTypes = ArrayHelper::merge($mimeTypes, ArrayHelper::getValue(MediaInterface::MIME_TYPES, $mediaType));
        }
        return $mimeTypes;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        $field = self::getTypeFieldName();
        return $this->$field;
    }

    /**
     * @param string $value
     */
    public function setType(string $value): void
    {
        $field = self::getTypeFieldName();
        $this->$field = $value;
    }

    /**
     * Get Origin file name (if field exist in model)
     * @return string
     */
    public function getOriginName(): ?string
    {
        $field = self::getOriginNameFieldName();
        return $field ? $this->$field : null;
    }

    /**
     * Set Origin file name (if field exist in model)
     * @param string $value
     */
    public function setOriginName(string $value): void
    {
        $field = self::getOriginNameFieldName();
        if ($field) {
            $this->$field = $value;
        }
    }

    /**
     * Get file size (if field exist in model)
     * @return int
     */
    public function getSize(): ?int
    {
        $field = self::getSizeFieldName();
        return $this->$field;
    }

    /**
     * Set file size (if field exist in model)
     * @param int $value
     */
    public function setSize(int $value): void
    {
        $field = self::getSizeFieldName();
        if ($field) {
            $this->$field = $value;
        }
    }
}
