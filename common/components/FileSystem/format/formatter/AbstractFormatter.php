<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\format\formatter;

use Imagine\Image\ImageInterface;
use yii\base\BaseObject;
use yii\imagine\Image as ImageProcessor;

/**
 * Create thumbnail from original image
 */
abstract class AbstractFormatter extends BaseObject implements FileFormatterInterface
{
    /**
     * @inheritdoc
     */
    public function format(string &$content, string &$extension): string
    {
        $image = ImageProcessor::getImagine()->load($content);
        return $this->process($image, $extension);
    }

    /**
     * @inheritdoc
     */
    public function formatFromPath(string $path, string &$extension): string
    {
        $image = ImageProcessor::getImagine()->open($path);
        return $this->process($image, $extension);
    }

    /**
     * @inheritdoc
     */
    public function formatFromLocalResource(string $path, string &$extension): string
    {
        $stream = fopen($path, 'r+');
        $image = ImageProcessor::getImagine()->read($stream);
        fclose($stream);
        return $this->process($image, $extension);
    }

    /**
     * Process original file
     * @param ImageInterface $file
     * @param string $extension
     * @return string binary
     */
    abstract protected function process(ImageInterface $file, string &$extension);
}
