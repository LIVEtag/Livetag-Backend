<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use common\fixtures\StreamSessionFixture;
use common\fixtures\UserFixture;

return [
    'create' => [
        [
            'dataComment' => 'Create Like For Seller',
            'userId' => UserFixture::SELLER_2,
            'streamSessionId' => StreamSessionFixture::STREAM_SESSION_8_SHOP_2_ARCHIVED,
            'response' => [
                'id' => 'integer',
                'userId' => 'integer:=' . UserFixture::SELLER_2,
                'createdAt' => 'integer',
                'user' => $I->getUserResponse(),
            ]
        ],
        [
            'dataComment' => 'Create Like For Buyer',
            'userId' => UserFixture::BUYER_2,
            'streamSessionId' => StreamSessionFixture::STREAM_SESSION_8_SHOP_2_ARCHIVED,
            'response' => [
                'id' => 'integer',
                'userId' => 'integer:=' . UserFixture::BUYER_2,
                'createdAt' => 'integer',
                'user' => $I->getUserResponse(),
            ]
        ]
    ],
];
