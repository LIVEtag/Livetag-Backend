<?php
return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/main-local.php',
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=' . getenv('DB_TEST_HOST') . ';dbname=' . getenv('DB_TEST_NAME'),
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
