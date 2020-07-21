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
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_frontendUser',
            ]
        ],
        'session' => [
            'name' => '_frontendSessionId',
            'savePath' => dirname(__DIR__) . '/runtime/session',
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
