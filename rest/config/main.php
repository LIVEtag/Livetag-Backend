<?php

/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use rest\common\models\User;
use rest\components\api\UrlRule;
use rest\components\api\ErrorHandler;
use rest\modules\swagger\Module as SwaggerModule;
use rest\modules\v1\Module as V1Module;
use rest\modules\chat\Module as ChatModule;
use rest\modules\chat\controllers\ChannelController;
use yii\log\FileTarget;
use yii\web\JsonParser;
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
        'chat' => [
            'class' => ChatModule::class,
        ]
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
        'errorHandler' => [
            'class' => ErrorHandler::class
        ],
        'request' => [
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
            'trustedHosts' => [
                '10.1.0.1',
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
                        'POST new-password' => 'new-password',
                        'POST logout' => 'logout',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/pages' => 'v1/pages'
                    ],
                    'extraPatterns' => [
                        'GET <slug>' => 'view',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/channel' => 'chat/channel'
                    ],
                    'extraPatterns' => [
                        'PUT <id:\d+>/join' => ChannelController::ACTION_JOIN,
                        'PUT <id:\d+>/leave' => ChannelController::ACTION_LEAVE,
                        'GET <id:\d+>/message' => ChannelController::ACTION_GET_MESSAGES,
                        'POST <id:\d+>/message' => ChannelController::ACTION_ADD_MESSAGE,
                        'GET <id:\d+>/user' => ChannelController::ACTION_GET_USERS,
                        'POST <id:\d+>/user/<userId:\d+>' => ChannelController::ACTION_ADD_TO_CHAT,
                        'DELETE <id:\d+>/user/<userId:\d+>' => ChannelController::ACTION_REMOVE_FROM_CHAT,
                        'POST auth' => ChannelController::ACTION_AUTH,
                        'POST sign' => ChannelController::ACTION_SIGN,
                        //'GET demo/<route:\*>'=>
                    ],
                    'pluralize' => false,
                ],
                'demo-chat/<action:.*>' => 'chat/demo/index',
            ],
        ],
    ],
    'params' => $params,
];
