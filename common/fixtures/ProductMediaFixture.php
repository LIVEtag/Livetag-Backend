<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\FileSystem\format\FormatEnum;
use common\components\FileSystem\media\MediaTypeEnum;
use common\components\test\ActiveFixture;
use common\models\Product\ProductMedia;

class ProductMediaFixture extends ActiveFixture
{
    const PRODUCT_1_SHOP_1_MEDIA_1 = 1;
    const PRODUCT_1_SHOP_1_MEDIA_2 = 2;
    const PRODUCT_2_SHOP_1_MEDIA_1 = 3;
    const PRODUCT_2_SHOP_1_MEDIA_2 = 4;
    const PRODUCT_3_SHOP_1_MEDIA_1 = 5;
    const PRODUCT_3_SHOP_1_MEDIA_2 = 6;
    const PRODUCT_4_SHOP_1_MEDIA_1 = 7;
    const PRODUCT_4_SHOP_1_MEDIA_2 = 8;
    const PRODUCT_5_SHOP_1_MEDIA_1 = 9;
    const PRODUCT_5_SHOP_1_MEDIA_2 = 10;
    const PRODUCT_6_SHOP_1_MEDIA_1 = 11;
    const PRODUCT_6_SHOP_1_MEDIA_2 = 12;
    const PRODUCT_7_SHOP_1_MEDIA_1 = 13;
    const PRODUCT_7_SHOP_1_MEDIA_2 = 14;
    const PRODUCT_8_SHOP_1_MEDIA_1 = 15;
    const PRODUCT_8_SHOP_1_MEDIA_2 = 16;
    const PRODUCT_9_SHOP_2_MEDIA_1 = 17;
    const PRODUCT_9_SHOP_2_MEDIA_2 = 18;
    const PRODUCT_10_SHOP_2_MEDIA_1 = 19;
    const PRODUCT_10_SHOP_2_MEDIA_2 = 20;
    const PRODUCT_11_SHOP_2_MEDIA_1 = 21;
    const PRODUCT_11_SHOP_2_MEDIA_2 = 22;
    const PRODUCT_12_SHOP_2_MEDIA_1 = 23;
    const PRODUCT_12_SHOP_2_MEDIA_2 = 24;

    public $modelClass = ProductMedia::class;
    public $depends = [ProductFixture::class];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        $name = $this->generator->word;
        $textColor = $this->generator->hexColor;
        $backgroundColor = $this->generator->hexColor;

        $smallImage = $this->createS3UploadedData(
            (new ProductMedia)->getRelativePath(),
            $this->generator->imageGenerator(null, 100, 150, 'png', true, $name, $backgroundColor, $textColor),
        );

        $largeImage = $this->createS3UploadedData(
            (new ProductMedia)->getRelativePath(),
            $this->generator->imageGenerator(
                null,
                $this->generator->numberBetween(400, 600),
                $this->generator->numberBetween(600, 800),
                'png',
                true,
                $name,
                $backgroundColor,
                $textColor
            ),
        );

        return [
            'type' => MediaTypeEnum::TYPE_IMAGE,
            'createdAt' => $this->generator->incrementalTime,
            'originName' => $name . '.png',
            'path' => $largeImage,
            'size' => $this->generator->randomNumber(5),
            'formatted' => [
                FormatEnum::SMALL => $smallImage,
                FormatEnum::LARGE => $largeImage,
            ]
        ];
    }
}
