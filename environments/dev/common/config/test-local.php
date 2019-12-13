<?php

use yii\caching\DummyCache;

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host={{DB_TEST_HOST}};dbname={{DB_TEST_NAME}}',
            ]
        ],
        'container' => [
            'singletons' => [
                \Faker\Generator::class => function () {
                    $generator = \Faker\Factory::create('en_EN');
                    $generator->addProvider(new \common\components\test\faker\IncrementalTimeProvider($generator));
                    return $generator;
                },
            ]
        ],
    ]
);
