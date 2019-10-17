<?php

use common\fixtures\UserFixture;

$faker = Faker\Factory::create();
return [
    UserFixture::USER => [
        'id' => UserFixture::USER,
        'authKey' => \Yii::$app->getSecurity()->generateRandomString(),
        //password_0
        'passwordHash' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO',
        'passwordResetToken' => null,
        'role' => 'basic',
        'status' => 10,
        'createdAt' => $faker->unixTime,
        'updatedAt' => $faker->unixTime,
        'email' => 'user@test.com',
    ],
    UserFixture::DELETED => [
        'id' => UserFixture::DELETED,
        'authKey' => \Yii::$app->getSecurity()->generateRandomString(),
        //password_0
        'passwordHash' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO',
        'passwordResetToken' => null,
        'role' => 'basic',
        'status' => 0,
        'createdAt' => $faker->unixTime,
        'updatedAt' => $faker->unixTime,
        'email' => 'deleted@test.com',
    ]
];
