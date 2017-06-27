<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use rest\common\models\User;
use rest\components\api\UrlRule;
use rest\modules\swagger\Module as SwaggerModule;
use rest\modules\v1\Module as V1Module;
use yii\log\FileTarget;
use yii\web\JsonParser;
use yii\web\Request;
use yii\web\Response;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
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
                        'v1/user' => 'v1/auth',
                    ],
                    'extraPatterns' => [
                        'POST login/facebook' => 'facebook',
                        'POST login/linkedin' => 'linkedin',
                        'POST login/google' => 'google',
                        'POST login/twitter' => 'twitter',
                        'OPTIONS login/facebook' => 'options',
                        'OPTIONS login/linkedin' => 'options',
                        'OPTIONS login/google' => 'options',
                        'OPTIONS login/twitter' => 'options',
                    ],
                    'pluralize' => false,
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/user' => 'v1/access-token'
                    ],
                    'extraPatterns' => [
                        'POST login' => 'create',
                        'OPTIONS login' => 'options',
                        'POST login/email' => 'email',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/user' => 'v1/user'
                    ],
                    'extraPatterns' => [
                        'POST register' => 'create',
                        'OPTIONS register' => 'options',
                        'GET current' => 'current',
                        'OPTIONS current' => 'options',
                        'PATCH change-password' => 'change-password',
                        'POST recovery-password' => 'recovery-password',
                        'POST new-password' => 'new-password'
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
