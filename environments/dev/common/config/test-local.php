<?php

use yii\caching\DummyCache;

return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host={{DB_TEST_HOST}};dbname={{DB_TEST_NAME}}',
        ],
        'cache' => [
            'class' => DummyCache::class,
        ],
    ],
];
