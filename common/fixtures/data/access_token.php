<?php
/**
 * Copyright © 2008 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\fixtures\AccessTokenFixture;
use common\fixtures\UserFixture;

/** @var AccessTokenFixture $this */

return [
    AccessTokenFixture::ADMIN => [
        'userId' => UserFixture::ADMIN,
        'token' => 'admin',
    ],
    AccessTokenFixture::SELLER_1 => [
        'userId' => UserFixture::SELLER_1,
        'token' => 'seller1',
    ],
    AccessTokenFixture::SELLER_2 => [
        'userId' => UserFixture::SELLER_2,
        'token' => 'seller2',
    ],
    AccessTokenFixture::SELLER_3 => [
        'userId' => UserFixture::SELLER_3,
        'token' => 'seller3',
    ],
    AccessTokenFixture::DELETED => [
        'userId' => UserFixture::DELETED,
        'token' => 'deleted',
    ]
];
