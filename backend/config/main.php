<?php

use backend\models\User\User;
use dmstr\web\AdminLteAsset;
use kartik\grid\Module;
use yii\web\DbSession;

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
    'modules' => [
        'gridview' => [
            'class' => Module::class
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'authTimeout' => 60 * 15,
            'identityCookie' => [
                'name' => '_backendUser',
            ]
        ],
       
        'session' => [
            'class' => DbSession::class,
            'name' => '_backendSessionId',
            'savePath' => dirname(__DIR__) . '/runtime/session',
            'writeCallback' => static function ($session) {
                return [
                    'userId' => Yii::$app->user->id,
                    'agent' => Yii::$app->request->getUserAgent(),
                    'ip' => Yii::$app->request->getUserIP(),
                ];
              
            },
            'cookieParams' => ['httponly' => true, 'lifetime' => 60 * 15],
            'timeout' => 60 * 15, //session expire
            'useCookies' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                AdminLteAsset::class => [
                    'skin' => 'skin-black',
                ],
            ],
        ],
        'urlManager' => require __DIR__ . '/urlManager.php',
        'request' => [
            'trustedHosts' => [
                '10.1.0.1',
            ],
        ],
    ],
    'params' => $params,
];
