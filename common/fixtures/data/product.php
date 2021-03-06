<?php
use common\fixtures\ProductFixture;
use common\fixtures\ShopFixture;

return [
    ProductFixture::PRODUCT_1_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_1_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
        'options' => [
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XXS',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XS',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', S',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', M',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', L',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XL',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XXL',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XXXL',
            ],
        ]
    ],
    ProductFixture::PRODUCT_2_SHOP_1 => [
        'id' => ProductFixture::PRODUCT_2_SHOP_1,
        'shopId' => ShopFixture::SHOP_1,
        'options' => [
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XS',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', L',
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
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XL',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XXL',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => $this->generator->randomFloat(2, 1, 1000),
                'option' => $this->generator->colorName . ', XXXL',
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
        'externalId' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'Samsung Galaxy S21',
        'options' => [
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 99.99,
                'option' => 'S21 Phantom Pink',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 99.99,
                'option' => 'S21 Phantom Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 99.99,
                'option' => 'S21 Phantom Violet',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 299.99,
                'option' => 'S21+ Phantom Pink',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 299.99,
                'option' => 'S21+ Phantom Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 299.99,
                'option' => 'S21+ Phantom Violet',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 499.99,
                'option' => 'S21 Ultra Phantom Pink',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 499.99,
                'option' => 'S21 Ultra Phantom Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 499.99,
                'option' => 'S21 Ultra Phantom Violet',
            ]
        ]
    ],
    ProductFixture::PRODUCT_10_SHOP_2 => [
        'id' => ProductFixture::PRODUCT_10_SHOP_2,
        'externalId' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'Xiaomi Redmi Note 9 Pro',
        'options' => [
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 229.99,
                'option' => '6/64G Glacier White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 229.99,
                'option' => '6/64G Tropical Green',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 229.99,
                'option' => '6/64G Interstellar Grey'
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 269.99,
                'option' => '6/128G Glacier White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 269.99,
                'option' => '6/128G Tropical Green',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 269.99,
                'option' => '6/128G Interstellar Grey',
            ],
        ]
    ],
    ProductFixture::PRODUCT_11_SHOP_2 => [
        'id' => ProductFixture::PRODUCT_11_SHOP_2,
        'externalId' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'iPhone 12',
        'options' => [
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 829,
                'option' => '64Gb Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 829,
                'option' => '64Gb Blue',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 829,
                'option' => '64Gb Red',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 829,
                'option' => '64Gb White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 829,
                'option' => '64Gb Green',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 879,
                'option' => '128Gb Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 879,
                'option' => '128Gb Blue',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 879,
                'option' => '128Gb Red',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 879,
                'option' => '256Gb White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 879,
                'option' => '128Gb Green',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 979,
                'option' => '256Gb Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 979,
                'option' => '256Gb Blue',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 979,
                'option' => '256Gb Red',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 979,
                'option' => '256Gb White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 979,
                'option' => '256Gb Green',
            ],
        ]
    ],
    ProductFixture::PRODUCT_12_SHOP_2 => [
        'id' => ProductFixture::PRODUCT_12_SHOP_2,
        'externalId' => $this->generator->unique()->numberBetween(1, 1000),
        'shopId' => ShopFixture::SHOP_2,
        'title' => 'iPhone 12 Pro',
        'options' => [
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 999,
                'option' => '128Gb Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 999,
                'option' => '128Gb Blue',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 999,
                'option' => '128Gb Red',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 999,
                'option' => '256Gb White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 999,
                'option' => '128Gb Green',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1099,
                'option' => '256Gb Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1099,
                'option' => '256Gb Blue',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1099,
                'option' => '256Gb Red',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1099,
                'option' => '256Gb White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1099,
                'option' => '256Gb Green',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1299,
                'option' => '512Gb Black',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1299,
                'option' => '512Gb Blue',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1299,
                'option' => '512Gb Red',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1299,
                'option' => '512Gb White',
            ],
            [
                'sku' => $this->generator->unique()->uuid,
                'price' => 1299,
                'option' => '512Gb Green',
            ],
        ]
    ],
];
