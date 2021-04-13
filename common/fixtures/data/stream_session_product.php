<?php
use common\fixtures\ProductFixture;
use common\fixtures\StreamSessionFixture;
use common\fixtures\StreamSessionProductFixture;
use common\models\Product\StreamSessionProduct;

return [
    //New Session of Shop1 (5/8 products)
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
    //Active Session of Shop2 (4/4 products)
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
    //Expired Session of Shop1
    StreamSessionProductFixture::PRODUCT_13_SESSION_1 => [
        'id' => StreamSessionProductFixture::PRODUCT_13_SESSION_1,
        'productId' => ProductFixture::PRODUCT_1_SHOP_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
    ],
    StreamSessionProductFixture::PRODUCT_14_SESSION_1 => [
        'id' => StreamSessionProductFixture::PRODUCT_14_SESSION_1,
        'productId' => ProductFixture::PRODUCT_2_SHOP_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
    ],
    //Expired Session of Shop2
    StreamSessionProductFixture::PRODUCT_15_SESSION_2 => [
        'id' => StreamSessionProductFixture::PRODUCT_15_SESSION_2,
        'productId' => ProductFixture::PRODUCT_9_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
    ],
    StreamSessionProductFixture::PRODUCT_16_SESSION_2 => [
        'id' => StreamSessionProductFixture::PRODUCT_16_SESSION_2,
        'productId' => ProductFixture::PRODUCT_10_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
    ],
    //Archived Session of Shop2
    StreamSessionProductFixture::PRODUCT_17_SESSION_7 => [
        'id' => StreamSessionProductFixture::PRODUCT_17_SESSION_7,
        'productId' => ProductFixture::PRODUCT_9_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_7_SHOP_2_ARCHIVED,
    ],
    StreamSessionProductFixture::PRODUCT_18_SESSION_7 => [
        'id' => StreamSessionProductFixture::PRODUCT_18_SESSION_7,
        'productId' => ProductFixture::PRODUCT_10_SHOP_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_7_SHOP_2_ARCHIVED,
    ],
];
