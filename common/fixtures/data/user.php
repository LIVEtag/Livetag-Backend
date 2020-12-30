<?php

use common\fixtures\UserFixture;
use common\models\User;

return [
    UserFixture::ADMIN => [
        'id' => UserFixture::ADMIN,
        'status' => User::STATUS_ACTIVE,
        'email' => 'admin@nosend.net',
        'role' => User::ROLE_ADMIN,
    ],
    UserFixture::SELLER => [
        'id' => UserFixture::SELLER,
        'email' => 'seller@nosend.net',
        'role' => User::ROLE_SELLER,
    ],
    UserFixture::DELETED => [
        'id' => UserFixture::DELETED,
        'status' => User::STATUS_DELETED,
        'email' => 'deleted@nosend.net',
    ]
];
