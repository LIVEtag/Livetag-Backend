<?php
use common\fixtures\ProductFixture;
use common\fixtures\ShopFixture;

return [
    ProductFixture::PRODUCT_1_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_1_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
        'options' => [
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XXS',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XS',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'S',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'M',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'L',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XL',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XXL',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XXXL',
            ],
        ]
    ],
    ProductFixture::PRODUCT_2_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_2_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
        'options' => [
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XS',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'L',
            ]
        ]
    ],
    ProductFixture::PRODUCT_3_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_3_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
    ],
    ProductFixture::PRODUCT_4_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_4_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
        'options' => [
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XL',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XXL',
            ],
            [
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'color' => $this->generator->colorName,
                'size' => 'XXXL',
            ],
        ]
    ],
    ProductFixture::PRODUCT_5_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_5_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
    ],
    ProductFixture::PRODUCT_6_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_6_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
    ],
    ProductFixture::PRODUCT_7_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_7_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
    ],
    ProductFixture::PRODUCT_8_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_8_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
    ],
    /////////////////////////////////////////////
    ProductFixture::PRODUCT_9_SHOP_2 => [
        'id' => ProductFixture::PRODUCT_9_SHOP_2,
        'sku' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'Samsung Galaxy S21',
        'options' => [
            [
                'price' => 99.99,
                'version' => 'S21 Phantom Pink',
            ],
            [
                'price' => 99.99,
                'version' => 'S21 Phantom Black',
            ],
            [
                'price' => 99.99,
                'version' => 'S21 Phantom Violet',
            ],
            [
                'price' => 299.99,
                'version' => 'S21+ Phantom Pink',
            ],
            [
                'price' => 299.99,
                'version' => 'S21+ Phantom Black',
            ],
            [
                'price' => 299.99,
                'version' => 'S21+ Phantom Violet',
            ],
            [
                'price' => 499.99,
                'version' => 'S21 Ultra Phantom Pink',
            ],
            [
                'price' => 499.99,
                'version' => 'S21 Ultra Phantom Black',
            ],
            [
                'price' => 499.99,
                'version' => 'S21 Ultra Phantom Violet',
            ]
        ]
    ],
    ProductFixture::PRODUCT_10_SHOP_2 => [
        'id' => ProductFixture::PRODUCT_10_SHOP_2,
        'sku' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'Xiaomi Redmi Note 9 Pro',
        'options' => [
            [
                'price' => 229.99,
                'version' => '6/64G Glacier White',
            ],
            [
                'price' => 229.99,
                'version' => '6/64G Tropical Green',
            ],
            [
                'price' => 229.99,
                'version' => '6/64G Interstellar Grey'
            ],
            [
                'price' => 269.99,
                'version' => '6/128G Glacier White',
            ],
            [
                'price' => 269.99,
                'version' => '6/128G Tropical Green',
            ],
            [
                'price' => 269.99,
                'version' => '6/128G Interstellar Grey',
            ],
        ]
    ],
    ProductFixture::PRODUCT_11_SHOP_2 => [
        'id' => ProductFixture::PRODUCT_11_SHOP_2,
        'sku' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'iPhone 12',
        'options' => [
            [
                'price' => 829,
                'version' => '64Gb Black',
            ],
            [
                'price' => 829,
                'version' => '64Gb Blue',
            ],
            [
                'price' => 829,
                'version' => '64Gb Red',
            ],
            [
                'price' => 829,
                'version' => '64Gb White',
            ],
            [
                'price' => 829,
                'version' => '64Gb Green',
            ],
            [
                'price' => 879,
                'version' => '128Gb Black',
            ],
            [
                'price' => 879,
                'version' => '128Gb Blue',
            ],
            [
                'price' => 879,
                'version' => '128Gb Red',
            ],
            [
                'price' => 879,
                'version' => '256Gb White',
            ],
            [
                'price' => 879,
                'version' => '128Gb Green',
            ],
            [
                'price' => 979,
                'version' => '256Gb Black',
            ],
            [
                'price' => 979,
                'version' => '256Gb Blue',
            ],
            [
                'price' => 979,
                'version' => '256Gb Red',
            ],
            [
                'price' => 979,
                'version' => '256Gb White',
            ],
            [
                'price' => 979,
                'version' => '256Gb Green',
            ],
        ]
    ],
    ProductFixture::PRODUCT_12_SHOP_2 => [
        'id' => ProductFixture::PRODUCT_12_SHOP_2,
        'sku' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'iPhone 12 Pro',
        'options' => [
            [
                'price' => 999,
                'version' => '128Gb Black',
            ],
            [
                'price' => 999,
                'version' => '128Gb Blue',
            ],
            [
                'price' => 999,
                'version' => '128Gb Red',
            ],
            [
                'price' => 999,
                'version' => '256Gb White',
            ],
            [
                'price' => 999,
                'version' => '128Gb Green',
            ],
            [
                'price' => 1099,
                'version' => '256Gb Black',
            ],
            [
                'price' => 1099,
                'version' => '256Gb Blue',
            ],
            [
                'price' => 1099,
                'version' => '256Gb Red',
            ],
            [
                'price' => 1099,
                'version' => '256Gb White',
            ],
            [
                'price' => 1099,
                'version' => '256Gb Green',
            ],
            [
                'price' => 1299,
                'version' => '512Gb Black',
            ],
            [
                'price' => 1299,
                'version' => '512Gb Blue',
            ],
            [
                'price' => 1299,
                'version' => '512Gb Red',
            ],
            [
                'price' => 1299,
                'version' => '512Gb White',
            ],
            [
                'price' => 1299,
                'version' => '512Gb Green',
            ],
        ]
    ],
];
