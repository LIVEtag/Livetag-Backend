<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use yii\debug\Module as DebugModule;
use yii\gii\Module as GiiModule;
use rest\generators\crud\Generator;

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
            'baseUrl' => '',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => DebugModule::class,
        'dataPath' => '@backend/runtime/debug',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => GiiModule::class,
        'allowedIPs' => ['*'],
        'generators' => [
            'restcrud' => [
                'class' => Generator::class,
                'templates' => [
                    'default' => '@rest/generators/crud/template',
                ]
            ]
        ],
    ];
}

return $config;
