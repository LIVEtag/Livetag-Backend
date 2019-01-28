<?php
$faker = Faker\Factory::create();

return [
    [
        'authKey' => \Yii::$app->getSecurity()->generateRandomString(),
        'passwordHash' => '$2y$13$CXT0Rkle1EMJ/c1l5bylL.EylfmQ39O5JlHJVFpNn618OUS1HwaIi',
        'passwordResetToken' => 't5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv_' . time(),
        'role' => 'basic',
        'createdAt' => $faker->unixTime,
        'updatedAt' => $faker->unixTime,
        'email' => $faker->email,
    ],
    [
        'authKey' => \Yii::$app->getSecurity()->generateRandomString(),
        'passwordHash' => '$2y$13$g5nv41Px7VBqhS3hVsVN2.MKfgT3jFdkXEsMC4rQJLfaMa7VaJqL2',
        'passwordResetToken' => '4BSNyiZNAuxjs5Mty990c47sVrgllIi_' . time(),
        'role' => 'basic',
        'createdAt' => $faker->unixTime,
        'updatedAt' => $faker->unixTime,
        'email' => 'nicolas.dianna@hotmail.com',
        'status' => '0',
    ],
];
