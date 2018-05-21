<?php

use common\components\rbac\controllers\RbacController;
use common\components\rbac\PhpManager;
use common\models\User;
use yii\console\controllers\MigrateController;
use yii\log\FileTarget;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => PhpManager::class,
            'defaultRoles' => [
                User::ROLE_GUEST
            ],
            'itemFile' => '@common/components/rbac/items.php',
            'ruleFile' => '@common/components/rbac/rules.php',
        ],
    ],
    'controllerMap' => [
        'rbac' => [
            'class' => RbacController::class,
        ],
        'migrate' => [
            // https://yiiframework.com.ua/ru/doc/guide/2/db-migrations/#namespaced-migrations
            // ------------
            // some of base yii migrations have not namespace, it is problem
            // example:
            // 'yii\log\migrations' - yii DB log migration
            // you can only create new migration with namespace and run problem migration inside
            // <?php
            //    namespace ...
            //    require_once $yiiPath . "/log/migrations/m141106_185632_log_init.php";
            //    (new \m141106_185632_log_init)->up();
            'class' => MigrateController::class,
            'migrationPath' => null,
            'migrationNamespaces' => [
                'console\migrations', //base migrations
                'rest\modules\chat\migrations'// chat module migrations
            ],
        ],
    ],
    'params' => $params,
];
