<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use common\fixtures\StreamSessionFixture;
use common\fixtures\UserFixture;
use common\models\Analytics\StreamSessionEvent;

return [
    'create' => [
        [
            'dataComment' => 'View',
            'userId' => UserFixture::BUYER_2,
            'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
            'request' => [
                'data' => [
                    'type' => StreamSessionEvent::TYPE_VIEW,
                    'payload' => null
                ]
            ]
        ],
        [
            'dataComment' => 'View',
            'userId' => UserFixture::BUYER_1,
            'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
            'request' => [
                'data' => [
                    'type' => StreamSessionEvent::TYPE_VIEW,
                    'payload' => null
                ]
            ]
        ]
    ],
];
