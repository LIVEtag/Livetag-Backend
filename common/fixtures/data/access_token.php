<?php
/**
 * Copyright © 2008 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\fixtures\AccessTokenFixture;
use common\fixtures\UserFixture;

return [
    AccessTokenFixture::USER => [
        'id' => AccessTokenFixture::USER,
        'userId' => UserFixture::USER,
        'token' => 'user',
        'userIp' => '127.0.0.1',
        'userAgent' => 'Symfony BrowserKit',
        'expiredAt' => time() + 30000000,
        'createdAt' => 0,
    ],
    AccessTokenFixture::DELETED => [
        'id' => AccessTokenFixture::DELETED,
        'userId' => UserFixture::DELETED,
        'token' => 'deleted',
        'userIp' => '127.0.0.1',
        'userAgent' => 'Symfony BrowserKit',
        'expiredAt' => time() + 30000000,
        'createdAt' => 0,
    ]
];
