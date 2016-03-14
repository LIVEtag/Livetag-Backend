<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'rest-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'rest\modules\v1\Module',
        ],
        'v2' => [
            'class' => 'rest\modules\v2\Module',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'rest\common\models\User',
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'acceptParams' => ['version' => 'v1']
        ],
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'rest\components\api\UrlRule',
                    'controller' => [
                        'v1/user',
                        'v1/access-token'
                    ],
                    'extraPatterns' => [
                        'GET current' => 'current',
                    ],
                    'pluralize' => true,
                ],
                [
                    'class' => 'rest\components\api\UrlRule',
                    'controller' => [
                        'v2/user',
                        'v2/access-token',
                    ],
                    'extraPatterns' => [
                        'GET current' => 'current',
                    ],
                    'pluralize' => true,
                ],
            ],
        ],
    ],
    'params' => $params,
];
