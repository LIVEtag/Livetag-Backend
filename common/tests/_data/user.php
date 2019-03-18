<?php

$faker = Faker\Factory::create();
return [
    [
        'authKey' => \Yii::$app->getSecurity()->generateRandomString(),
        //password_0
        'passwordHash' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO',
        'passwordResetToken' => 'ExzkCOaYc1L8IOBs4wdTGGbgNiG3Wz1I_1402312317',
        'role' => 'basic',
        'createdAt' => $faker->unixTime,
        'updatedAt' => $faker->unixTime,
        'email' => 'bayer.hudson@test.com',
    ],
];
