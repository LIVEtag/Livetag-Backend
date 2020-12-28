<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@rest', dirname(dirname(__DIR__)) . '/rest');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

if (file_exists(__DIR__ . '/../../.env')) {
    //@see https://github.com/vlucas/phpdotenv#putenv-and-getenv
    //allow to override enviroment variables with .env values
    $dotenv = Dotenv\Dotenv::createUnsafeMutable(__DIR__ . '/../../');
    $dotenv->load();
    //minimum project execution requirements
    $dotenv->required([
        'DB_HOST',
        'DB_NAME',
        'DB_USERNAME',
        'DB_PASSWORD',
        'SUPPORT_EMAIL'//required for tests
    ])
    ->notEmpty();
}