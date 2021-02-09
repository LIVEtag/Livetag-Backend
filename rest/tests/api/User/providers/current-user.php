<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use common\fixtures\UserFixture;

return [
    'current' => [
        [
            'dataComment' => 'Current user For Seller',
            'userId' => UserFixture::SELLER_1,
        ],
        [
            'dataComment' => 'Current usernt For Buyer',
            'userId' => UserFixture::BUYER_1,
        ]
    ],
];
