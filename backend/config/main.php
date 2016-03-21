<?php

use backend\modules\rbac\Module as RbacModule;
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
    'modules' => [
        'swagger' => [
            'class' => SwaggerModule::class,
        ],
        'rbac' => [
            'class' => RbacModule::class,
            'layout' => 'left-menu',
            'mainLayout' => '@app/views/layouts/main.php',
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
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];
