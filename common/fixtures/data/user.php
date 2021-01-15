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
    UserFixture::SELLER_1 => [
        'id' => UserFixture::SELLER_1,
        'email' => 'seller1@nosend.net',
        'role' => User::ROLE_SELLER,
    ],
    UserFixture::SELLER_2 => [
        'id' => UserFixture::SELLER_2,
        'email' => 'seller2@nosend.net',
        'role' => User::ROLE_SELLER,
    ],
    UserFixture::DELETED => [
        'id' => UserFixture::DELETED,
        'email' => 'blocked@nosend.net',
        'role' => User::ROLE_SELLER,
        'status' => User::STATUS_DELETED,
    ]
];
