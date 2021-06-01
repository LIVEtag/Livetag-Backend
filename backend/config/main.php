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
            'identityCookie' => [
                'name' => '_backendUser',
            ]
        ],
        'session' => [
            'class' => DbSession::class,
            'name' => '_backendSessionId',
            'writeCallback' => static function () {
                return [
                    'userId' => Yii::$app->user->id
                ];
            }
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                AdminLteAsset::class => [
                    'skin' => false,
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
