<?php

use backend\modules\swagger\Module as SwaggerModule;
use common\models\User;
use yii\log\FileTarget;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'layout' => 'container',
    'modules' => [
        'swagger' => [
            'class' => SwaggerModule::class,
            'layout' => '@app/views/layouts/main',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'rules' => [
                '' => '',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>/',
                '<module:\w+>/<controller:\w+>' => '<module>/<controller>/',
                '<module:\w+>/<controller:\w+>/<action:.*>' => '<module>/<controller>/<action>/',
            ],
        ],
    ],
    'params' => $params,
];
