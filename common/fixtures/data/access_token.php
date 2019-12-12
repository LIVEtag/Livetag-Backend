<?php
/**
 * Copyright © 2008 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\fixtures\AccessTokenFixture;
use common\fixtures\UserFixture;

/** @var AccessTokenFixture $this */

return [
    UserFixture::USER => [
        'userId' => UserFixture::USER,
        'token' => 'user',
    ],
    AccessTokenFixture::DELETED => [
        'userId' => UserFixture::DELETED,
        'token' => 'deleted',
    ]
];
