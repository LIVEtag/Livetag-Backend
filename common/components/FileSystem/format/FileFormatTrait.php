<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\format;

use common\components\db\ModelSaveException;
use common\components\FileSystem\format\formatter\FileFormatterInterface;
use common\helpers\FileHelper;
use common\helpers\LogHelper;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Trait FileFormatTrait, implements FileFormatInterface.
 * @see FileFormatInterface
 */
trait FileFormatTrait
{

    /**
     * Get name of field, that used for storing formatted data (as array)
     * @return string
     */
    public static function getFormattedFieldName(): string
    {
        return 'formatted';
    }

    /**
     * @return array|null
     */
    protected function getFormatted(): ?array
    {
        $formattedField = self::getFormattedFieldName();
        return $this->$formattedField ?: [];
    }

    /**
     * @param array $value
     */
    protected function setFormatted(array $value)
    {
        $formattedField = self::getFormattedFieldName();
        $this->$formattedField = $value;
    }

    /**
     * Return all urls formatted images
     * Used for API responce
     * @return array|null
     */
    public function getFormattedUrls(): ?array
    {
        $output = [];
        foreach ($this->getFormatted() as $name => $path) {
            $output[$name] = FileHelper::getUrlByPath($path);
        }
        if (count($output) < 1) {
            return null;
        }
        return $output;
    }

    /**
     * Get single formatted item url by format name
     * @param $name
     * @return string|null
     * @throws \Exception
     */
    public function getFormattedUrlByName(string $name): ?string
    {
        if (!in_array($name, FormatEnum::ALLOWED_FORMAT_LIST)) {
            throw new Exception('Undefined format name.');
        }
        $path = ArrayHelper::getValue($this->formatted, $name);
        if ($path && mb_strlen($path) > 0) {
            return FileHelper::getUrlByPath($path);
        }
        return null;
    }

    /**
     * Remove all formatted items by path
     */
    public function removeFormattedItems(): void
    {
        foreach ($this->getFormatted() as $path) {
            FileHelper::deleteFileByPath($path);
        }
    }

    /**
     * Generate images of allowed formats.
     * @param string $path S3 path of original file
     * @param UploadedFile $file
     * @return bool
     */
    public function createFormat(string $path, UploadedFile $file): bool
    {
        try {
            $result = [];
            foreach (array_keys($this->getFormatters()) as $formatName) {
                $result[$formatName] = $this->runFormat($path, $file, $formatName);
            }
            $this->setFormatted($result);
            return true;
        } catch (Throwable $ex) {
            $this->addError(self::getFormattedFieldName(), 'Failed to create formatted files:' . $ex->getMessage());
            LogHelper::error('Failed to create formatted files', 'file', LogHelper::extraForException($this, $ex));
            return false;
        }
    }

    /**
     * @param string $originPath S3 path of original file
     * @param UploadedFile $file
     * @param string $formatName
     * @return string
     * @throws ModelSaveException
     * @throws InvalidConfigException
     */
    protected function runFormat(string $originPath, UploadedFile $file, string $formatName): string
    {
        $pathParts = pathinfo($originPath);
        $extension = $pathParts['extension'];
        $formattedContent = $this->getFormatter($formatName)->formatFromLocalResource($file->tempName, $extension);
        $path = FileHelper::genUniqPath("{$pathParts['dirname']}/{$pathParts['filename']}", $extension);
        if (!FileHelper::setFileContentToPath($formattedContent, $path)) {
            throw new RuntimeException('Can\'t write file to file system'); //!!
        }
        unset($formattedContent);
        return $path;
    }

    /**
     * Create formatter object by name
     * @param string $formatName
     * @return FileFormatterInterface
     * @throws InvalidConfigException
     */
    protected function getFormatter(string $formatName): FileFormatterInterface
    {
        /** @var FileFormatterInterface $formatter */
        $formatter = Yii::createObject(ArrayHelper::getValue($this->getFormatters(), $formatName));
        if (!($formatter instanceof FileFormatterInterface)) {
            throw new RuntimeException("Invalid formatter {$formatName}");
        }
        return $formatter;
    }
}
