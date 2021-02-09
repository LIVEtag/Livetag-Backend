<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use common\fixtures\StreamSessionFixture;
use common\fixtures\UserFixture;

return [
    'create' => [
        [
            'dataComment' => 'Create Comment For Seller',
            'userId' => UserFixture::SELLER_2,
            'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
            'request' => [
                'data' => [
                    'message' => $I->generator->text(255),
                ]
            ],
            'response' => [
                'id' => 'integer',
                'userId' => 'integer:=' . UserFixture::SELLER_2,
                'message' => 'string',
                'createdAt' => 'integer',
                'user' => $I->getUserResponse(),
            ]
        ],
        [
            'dataComment' => 'Create Comment For Buyer',
            'userId' => UserFixture::BUYER_1,
            'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
            'request' => [
                'data' => [
                    'message' => $I->generator->text(255),
                ]
            ],
            'response' => [
                'id' => 'integer',
                'userId' => 'integer:=' . UserFixture::BUYER_1,
                'message' => 'string',
                'createdAt' => 'integer',
                'user' => $I->getUserResponse(),
            ]
        ]
    ],
];
