<?php
use common\fixtures\ProductFixture;
use common\fixtures\StreamSessionFixture;
use common\fixtures\StreamSessionProductFixture;
use common\models\Product\StreamSessionProduct;

return [
    StreamSessionProductFixture::PRODUCT_1_SESSION_3 => [
        'id' => StreamSessionProductFixture::PRODUCT_1_SESSION_3,
        'productId' => ProductFixture::PRODUCT_1_SHOP_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW,
    ],
    StreamSessionProductFixture::PRODUCT_2_SESSION_3 => [
        'id' => StreamSessionProductFixture::PRODUCT_2_SESSION_3,
        'productId' => ProductFixture::PRODUCT_2_SHOP_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW,
        'status' => StreamSessionProduct::STATUS_PRESENTED,
    ],
    StreamSessionProductFixture::PRODUCT_3_SESSION_3 => [
        'id' => StreamSessionProductFixture::PRODUCT_3_SESSION_3,
        'productId' => ProductFixture::PRODUCT_3_SHOP_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW,
    ],
    StreamSessionProductFixture::PRODUCT_4_SESSION_3 => [
        'id' => StreamSessionProductFixture::PRODUCT_4_SESSION_3,
        'productId' => ProductFixture::PRODUCT_4_SHOP_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW,
    ],
    StreamSessionProductFixture::PRODUCT_5_SESSION_3 => [
        'id' => StreamSessionProductFixture::PRODUCT_5_SESSION_3,
        'productId' => ProductFixture::PRODUCT_5_SHOP_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW,
    ],
    StreamSessionProductFixture::PRODUCT_9_SESSION_4 => [
        'id' => StreamSessionProductFixture::PRODUCT_9_SESSION_4,
        'productId' => ProductFixture::PRODUCT_9_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'status' => StreamSessionProduct::STATUS_PRESENTED,
    ],
    StreamSessionProductFixture::PRODUCT_10_SESSION_4 => [
        'id' => StreamSessionProductFixture::PRODUCT_10_SESSION_4,
        'productId' => ProductFixture::PRODUCT_10_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
    ],
    StreamSessionProductFixture::PRODUCT_11_SESSION_4 => [
        'id' => StreamSessionProductFixture::PRODUCT_11_SESSION_4,
        'productId' => ProductFixture::PRODUCT_11_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
    ],
    StreamSessionProductFixture::PRODUCT_12_SESSION_4 => [
        'id' => StreamSessionProductFixture::PRODUCT_12_SESSION_4,
        'productId' => ProductFixture::PRODUCT_12_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
    ],
];
