<?php
use common\fixtures\ProductFixture;
use common\fixtures\StreamSessionFixture;
use common\fixtures\StreamSessionProductEventFixture;
use common\fixtures\UserFixture;
use common\models\Analytics\StreamSessionProductEvent;
use common\models\Product\StreamSessionProduct;

return [
    //Expired Session of Shop1
    StreamSessionProductEventFixture::EVENT_1_SESSION_1 => [
        'id' => StreamSessionProductEventFixture::EVENT_1_SESSION_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'productId' => ProductFixture::PRODUCT_1_SHOP_1,
        'userId' => UserFixture::BUYER_1,
        'payload' => [
            'price' => $this->generator->randomFloat(2, 1, 1000),
            'color' => $this->generator->colorName,
            'size' => 'S',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_2_SESSION_1 => [
        'id' => StreamSessionProductEventFixture::EVENT_2_SESSION_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'productId' => ProductFixture::PRODUCT_1_SHOP_1,
        'userId' => UserFixture::BUYER_1,
        'payload' => [
            'price' => $this->generator->randomFloat(2, 1, 1000),
            'color' => $this->generator->colorName,
            'size' => 'M',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_3_SESSION_1 => [
        'id' => StreamSessionProductEventFixture::EVENT_3_SESSION_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'productId' => ProductFixture::PRODUCT_2_SHOP_1,
        'userId' => UserFixture::BUYER_1,
        'payload' => [
            'price' => $this->generator->randomFloat(2, 1, 1000),
            'color' => $this->generator->colorName,
            'size' => 'XS',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_4_SESSION_1 => [
        'id' => StreamSessionProductEventFixture::EVENT_4_SESSION_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'productId' => ProductFixture::PRODUCT_2_SHOP_1,
        'userId' => UserFixture::BUYER_2,
        'payload' => [
            'price' => $this->generator->randomFloat(2, 1, 1000),
            'color' => $this->generator->colorName,
            'size' => 'XS',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_5_SESSION_1 => [
        'id' => StreamSessionProductEventFixture::EVENT_5_SESSION_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'productId' => ProductFixture::PRODUCT_2_SHOP_1,
        'userId' => UserFixture::BUYER_2,
        'payload' => [
            'price' => $this->generator->randomFloat(2, 1, 1000),
            'color' => $this->generator->colorName,
            'size' => 'L',
        ],
    ],
    //Expired Session of Shop2
    StreamSessionProductEventFixture::EVENT_6_SESSION_2 => [
        'id' => StreamSessionProductEventFixture::EVENT_6_SESSION_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'productId' => ProductFixture::PRODUCT_9_SHOP_2,
        'userId' => UserFixture::BUYER_2,
        'payload' => [
            'price' => 99.99,
            'version' => 'S21 Phantom Black',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_7_SESSION_2 => [
        'id' => StreamSessionProductEventFixture::EVENT_7_SESSION_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'productId' => ProductFixture::PRODUCT_9_SHOP_2,
        'userId' => UserFixture::BUYER_1,
        'payload' => [
            'price' => 99.99,
            'version' => 'S21 Phantom Pink',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_8_SESSION_2 => [
        'id' => StreamSessionProductEventFixture::EVENT_8_SESSION_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'productId' => ProductFixture::PRODUCT_11_SHOP_2,
        'userId' => UserFixture::BUYER_2,
        'payload' => [
            'price' => 979,
            'version' => '256Gb Blue',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_9_SESSION_2 => [
        'id' => StreamSessionProductEventFixture::EVENT_9_SESSION_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'productId' => ProductFixture::PRODUCT_11_SHOP_2,
        'userId' => UserFixture::BUYER_2,
        'payload' => [
            'price' => 829,
            'version' => '64Gb Black',
        ],
    ],
    //Active Session of Shop2
    StreamSessionProductEventFixture::EVENT_10_SESSION_4 => [
        'id' => StreamSessionProductEventFixture::EVENT_10_SESSION_4,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'productId' => ProductFixture::PRODUCT_10_SHOP_2,
        'userId' => UserFixture::BUYER_1,
        'payload' => [
            'price' => 229.99,
            'version' => '6/64G Glacier White',
        ],
    ],
    StreamSessionProductEventFixture::EVENT_11_SESSION_4 => [
        'id' => StreamSessionProductEventFixture::EVENT_11_SESSION_4,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'productId' => ProductFixture::PRODUCT_10_SHOP_2,
        'userId' => UserFixture::BUYER_2,
        'payload' => [
            'price' => 229.99,
            'version' => '6/64G Tropical Green',
        ],
    ],
    //Archived Session of Shop2
    StreamSessionProductEventFixture::EVENT_12_SESSION_7 => [
        'id' => StreamSessionProductEventFixture::EVENT_12_SESSION_7,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_7_SHOP_2_ARCHIVED,
        'productId' => ProductFixture::PRODUCT_9_SHOP_2,
        'type' => StreamSessionProductEvent::TYPE_PRODUCT_CREATE,
        'userId' => null,
        'payload' => [
            'status' => StreamSessionProduct::STATUS_DISPLAYED,
        ],
    ],
    StreamSessionProductEventFixture::EVENT_13_SESSION_8 => [
        'id' => StreamSessionProductEventFixture::EVENT_13_SESSION_8,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_7_SHOP_2_ARCHIVED,
        'productId' => ProductFixture::PRODUCT_10_SHOP_2,
        'type' => StreamSessionProductEvent::TYPE_PRODUCT_CREATE,
        'userId' => null,
        'payload' => [
            'status' => StreamSessionProduct::STATUS_DISPLAYED,
        ],
    ],
];
