<?php

use common\fixtures\ProductFixture;
use common\fixtures\ShopFixture;
use common\models\Product\Product;

return [
    ProductFixture::PRODUCT_HIDDEN_1 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_1,
        'shopId' => ShopFixture::STORE_1,
        'status' => Product::STATUS_HIDDEN,
        'options' => [
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ],
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ],
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ]
        ]
    ],
    ProductFixture::PRODUCT_HIDDEN_2 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_2,
        'shopId' => ShopFixture::STORE_2,
        'status' => Product::STATUS_HIDDEN,
    ],
    ProductFixture::PRODUCT_HIDDEN_3 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_3,
        'shopId' => ShopFixture::STORE_1,
        'status' => Product::STATUS_HIDDEN,
    ],
    ProductFixture::PRODUCT_HIDDEN_4 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_4,
        'shopId' => ShopFixture::STORE_2,
        'status' => Product::STATUS_HIDDEN,
    ],
    ProductFixture::PRODUCT_PRESENTED_1 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_1,
        'shopId' => ShopFixture::STORE_1,
        'status' => Product::STATUS_PRESENTED,
    ],
    ProductFixture::PRODUCT_PRESENTED_2 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_2,
        'shopId' => ShopFixture::STORE_2,
        'status' => Product::STATUS_PRESENTED,
        'options' => [
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ],
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ],
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ]
        ]
    ],
    ProductFixture::PRODUCT_PRESENTED_3 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_3,
        'shopId' => ShopFixture::STORE_1,
        'status' => Product::STATUS_PRESENTED,
    ],
    ProductFixture::PRODUCT_PRESENTED_4 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_4,
        'shopId' => ShopFixture::STORE_2,
        'status' => Product::STATUS_PRESENTED,
    ],
    ProductFixture::PRODUCT_DISPLAYED_1 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_1,
        'shopId' => ShopFixture::STORE_1,
        'status' => Product::STATUS_DISPLAYED,
        'options' => [
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ],
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ],
            [
                'price'=> $this->generator->randomFloat(2, 1, 10000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            ]
        ]
    ],
    ProductFixture::PRODUCT_DISPLAYED_2 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_2,
        'shopId' => ShopFixture::STORE_2,
        'status' => Product::STATUS_DISPLAYED,

    ],
    ProductFixture::PRODUCT_DISPLAYED_3 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_3,
        'shopId' => ShopFixture::STORE_1,
        'status' => Product::STATUS_DISPLAYED,
    ],
    ProductFixture::PRODUCT_DISPLAYED_4 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_4,
        'shopId' => ShopFixture::STORE_2,
        'status' => Product::STATUS_DISPLAYED,
    ],
];
