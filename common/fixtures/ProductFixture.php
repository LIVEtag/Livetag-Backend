<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Product\Product;

/**
 * Class ProductFixture
 */
class ProductFixture extends ActiveFixture
{
    const PRODUCT_1_SHOP_1 = 1;
    const PRODUCT_2_SHOP_1 = 2;
    const PRODUCT_3_SHOP_1 = 3;
    const PRODUCT_4_SHOP_1 = 4;
    const PRODUCT_5_SHOP_1 = 5;
    const PRODUCT_6_SHOP_1 = 6;
    const PRODUCT_7_SHOP_1 = 7;
    const PRODUCT_8_SHOP_1 = 8;
    const PRODUCT_9_SHOP_2 = 9;
    const PRODUCT_10_SHOP_2 = 10;
    const PRODUCT_11_SHOP_2 = 11;
    const PRODUCT_12_SHOP_2 = 12;

    public $modelClass = Product::class;
    public $depends = [ShopFixture::class];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'sku' => $this->generator->uuid,
            'title' => $this->generator->text(20),
            'photo' => 'https://picsum.photos/seed/' . $this->generator->randomElement(range(1, 100)) . '/200',
            'link' => $this->generator->url,
            'status' => Product::STATUS_ACTIVE,
            'options' => [
                [
                    'price' => $this->generator->randomFloat(2, 1, 1000),
                    'color' => $this->generator->colorName,
                    'size' => $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
                ]
            ],
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
