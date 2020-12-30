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
    AccessTokenFixture::SELLER => [
        'userId' => UserFixture::SELLER,
        'token' => 'seller',
    ],
    AccessTokenFixture::DELETED => [
        'userId' => UserFixture::DELETED,
        'token' => 'deleted',
    ]
];
