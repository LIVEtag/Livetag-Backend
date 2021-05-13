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
 * Resize original image
 */
class Resize extends AbstractFormatter
{
    /** @var integer */
    public $width = 150;

    /** @var integer */
    public $height = 200;

    /**
     * Process original file
     * @param ImageInterface $file
     * @param string $extension
     * @return string binary
     */
    protected function process(ImageInterface $file, string &$extension)
    {
        $extension = 'png';

        $rotatedImage = ImageProcessor::autorotate($file); // rotate according to exif data
        $image = ImageProcessor::resize(
            $rotatedImage,
            $this->width,
            $this->height
        );
        return $image->get($extension);
    }
}
