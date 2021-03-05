<?php
use common\fixtures\StreamSessionFixture;
use common\fixtures\StreamSessionStatisticFixture;

return [
    StreamSessionStatisticFixture::STATISTIC_SESSION_1 => [
        'id' => StreamSessionStatisticFixture::STATISTIC_SESSION_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'addToCartCount' => 5,
    ],
    StreamSessionStatisticFixture::STATISTIC_SESSION_2 => [
        'id' => StreamSessionStatisticFixture::STATISTIC_SESSION_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'addToCartCount' => 4,
    ],
    StreamSessionStatisticFixture::STATISTIC_SESSION_3 => [
        'id' => StreamSessionStatisticFixture::STATISTIC_SESSION_3,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW,
        'viewsCount' => 0,
        'addToCartCount' => 0,
    ],
    StreamSessionStatisticFixture::STATISTIC_SESSION_4 => [
        'id' => StreamSessionStatisticFixture::STATISTIC_SESSION_4,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'addToCartCount' => 2,
    ],
];
