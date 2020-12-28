<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\debug\Module as DebugModule;
use backend\generators\crud\Generator as CrudGenerator;
use backend\generators\model\Generator as ModelGenerator;
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
        'allowedIPs' => ['127.0.0.1', '::1', '172.*'],
        'generators' => [ //here
            'crud' => [
                'class' => CrudGenerator::class,
                'templates' => [
                    'default' => '@backend/generators/crud/default',
                ]
            ],
            'model' => [
                'class' => ModelGenerator::class,
                'templates' => [
                    'default' => '@backend/generators/model/default',
                ]
            ],
        ],
    ];
}

return $config;
