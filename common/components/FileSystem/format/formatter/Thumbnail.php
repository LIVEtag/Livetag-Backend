<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\format\formatter;

use Imagine\Image\ImageInterface;
use yii\imagine\BaseImage;
use yii\imagine\Image as ImageProcessor;

/**
 * Create thumbnail from original image
 */
class Thumbnail extends AbstractFormatter
{
    /** @var integer */
    public $width = 150;

    /** @var integer */
    public $height = 200;

    /** @var integer */
    public $mode = ImageInterface::THUMBNAIL_INSET;

    /**
     * Process original file
     * @param ImageInterface $file
     * @param string $extension
     * @return string binary
     */
    protected function process(ImageInterface $file, string &$extension)
    {
        $extension = 'png';
        BaseImage::$thumbnailBackgroundAlpha = 0;

        $rotatedImage = ImageProcessor::autorotate($file); // rotate according to exif data
        $image = ImageProcessor::thumbnail(
            $rotatedImage,
            $this->width,
            $this->height,
            $this->mode,
        );
        return $image->get($extension);
    }
}
