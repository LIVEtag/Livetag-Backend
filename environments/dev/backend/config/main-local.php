<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\debug\Module as DebugModule;
use yii\gii\generators\crud\Generator;
use yii\gii\Module as GiiModule;

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
            'baseUrl' => '',
            'csrfCookie' => [
                'path' => '/'
            ],
        ],
        'user' => [
            'identityCookie' => [
                'path'=>'/'
            ]
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => DebugModule::class,
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => GiiModule::class,
        'allowedIPs' => ['127.0.0.1', '::1', '172.18.0.*'],
        'generators' => [ //here
            'crud' => [
                'class' => Generator::class,
                'templates' => [
                    'adminlte' => '@backend/generators/crud/simple',
                ]
            ]
        ],
    ];
}

return $config;
