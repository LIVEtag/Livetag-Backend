<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use rest\common\models\User;
use rest\components\api\UrlRule;
use rest\modules\swagger\Module as SwaggerModule;
use rest\modules\v1\Module as V1Module;
use rest\modules\v2\Module as V2Module;
use yii\log\FileTarget;
use yii\web\JsonParser;
use yii\web\Request;
use yii\web\Response;

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
        'swagger' => [
            'class' => SwaggerModule::class,
        ],
        'v1' => [
            'class' => V1Module::class,
        ],
        'v2' => [
            'class' => V2Module::class,
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => User::class,
            'enableSession' => false,
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
        'response' => [
            'format' => Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'acceptParams' => ['version' => 'v1']
        ],
        'request' => [
            'class' => Request::class,
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'urlManager' => [
            'rules' => [
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'swagger/main',
                    ],
                    'extraPatterns' => [
                        'GET json' => 'json',
                        'GET history' => 'history',
                    ],
                    'pluralize' => false,
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/user',
                        'v1/access-token'
                    ],
                    'extraPatterns' => [
                        'GET current' => 'current',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v2/user',
                        'v2/access-token',
                    ],
                    'extraPatterns' => [
                        'GET current' => 'current',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
