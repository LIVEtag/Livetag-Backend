<?php

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
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_backendUser',
            ]
        ],
        // Для проверки данных, детали смотреть в классе AccessService
//        'dataAccessManager' => [
//            'class' => \common\components\rbac\data\AccessService::class,
//            'itemFile' => '@common/components/rbac/data/items.php',
//        ],
        'session' => [
            'name' => '_backendSessionId',
            'savePath' => dirname(__DIR__) . '/runtime/session',
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
                '<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>/',
                '<module:\w+>/<controller:[\w\-]+>' => '<module>/<controller>/',
                '<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>/',
            ],
        ],
        'request' => [
            'trustedHosts' => [
                '10.1.0.1',
            ],
        ],
    ],
    'params' => $params,
];
