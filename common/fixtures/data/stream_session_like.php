<?php

use common\fixtures\StreamSessionFixture;
use common\fixtures\StreamSessionLikeFixture;
use common\fixtures\UserFixture;

return [
    [
        'id' => StreamSessionLikeFixture::LIKE_1_SESSION_8,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_8_SHOP_2_ARCHIVED,
        'userId' => UserFixture::SELLER_2,
        'createdAt' => StreamSessionFixture::STREAM_SESSION_8_STARTED_AT,
    ],
    [
        'id' => StreamSessionLikeFixture::LIKE_2_SESSION_8,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_8_SHOP_2_ARCHIVED,
        'userId' => UserFixture::BUYER_2,
        'createdAt' => StreamSessionFixture::STREAM_SESSION_8_STARTED_AT,
    ],
    [
        'id' => StreamSessionLikeFixture::LIKE_3_SESSION_8,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_8_SHOP_2_ARCHIVED,
        'userId' => UserFixture::BUYER_2,
        'createdAt' => StreamSessionFixture::STREAM_SESSION_8_STARTED_AT + 10,
    ],
    [
        'id' => StreamSessionLikeFixture::LIKE_4_SESSION_8,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_8_SHOP_2_ARCHIVED,
        'userId' => UserFixture::BUYER_2,
        'createdAt' => StreamSessionFixture::STREAM_SESSION_8_STARTED_AT + 20,
    ],
    [
        'id' => StreamSessionLikeFixture::LIKE_5_SESSION_8,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_8_SHOP_2_ARCHIVED,
        'userId' => UserFixture::BUYER_2,
        'createdAt' => StreamSessionFixture::STREAM_SESSION_8_STARTED_AT + 30,
    ],
];
