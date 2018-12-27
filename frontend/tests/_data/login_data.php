<?php
$faker = Faker\Factory::create();

return [
    [
        'username' => 'erau',
        'authKey' => \Yii::$app->getSecurity()->generateRandomString(),
        // password_0
        'passwordHash' => '$2y$13$nJ1WDlBaGcbCdbNC5.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne',
        'passwordResetToken' => 'RkD_Jw0_8HEedzLk7MM-ZKEFfYR7VbMr_1392559490',
        'createdAt' => $faker->unixTime,
        'updatedAt' => $faker->unixTime,
        'role' => 'basic',
        'email' => $faker->email,
    ],
];
