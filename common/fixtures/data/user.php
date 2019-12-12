<?php

use common\fixtures\UserFixture;
use common\models\User;

return [
    UserFixture::USER => [
        'id' => UserFixture::USER,
        'email' => 'user@test.com',
    ],
    UserFixture::DELETED => [
        'id' => UserFixture::DELETED,
        'status' => User::STATUS_DELETED,
        'email' => 'deleted@test.com',
    ]
];
