<?php

/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\models\User;
use rest\components\api\UrlRule;
use rest\components\api\ErrorHandler;
use rest\modules\swagger\Module as SwaggerModule;
use rest\modules\v1\Module as V1Module;
use rest\modules\chat\Module as ChatModule;
use rest\components\validation\validators as RestValidators;
use rest\common\models\User as RestUser;
use rest\modules\chat\controllers\ChannelController;
use yii\data\Pagination;
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
    ],
    'components' => [
        'user' => [
            'class' => RestUser::class,
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
                        'POST login/<type:\w+>' => 'auth',
                        'OPTIONS login/<type:\w+>' => 'options',
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
                        'OPTIONS change-password' => 'options',
                        'POST recovery-password' => 'recovery-password',
                        'OPTIONS recovery-password' => 'options',
                        'POST new-password' => 'new-password',
                        'OPTIONS new-password' => 'options',
                        'POST logout' => 'logout',
                        'OPTIONS logout' => 'options',
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
                        'v1/config' => 'v1/config'
                    ],
                ],
            ],
        ],
    ],
    'container' => [
        'singletons' => [
            \rest\components\validation\ErrorListInterface::class => \rest\components\validation\ErrorList::class,
        ],
        'definitions' => [
            \yii\validators\StringValidator::class => RestValidators\StringValidator::class,
            \yii\validators\EmailValidator::class => RestValidators\EmailValidator::class,
            \yii\validators\FileValidator::class => RestValidators\FileValidator::class,
            \yii\validators\ImageValidator::class => RestValidators\ImageValidator::class,
            \yii\validators\BooleanValidator::class => RestValidators\BooleanValidator::class,
            \yii\validators\NumberValidator::class => RestValidators\NumberValidator::class,
            \yii\validators\DateValidator::class => RestValidators\DateValidator::class,
            \yii\validators\RangeValidator::class => RestValidators\RangeValidator::class,
            \yii\validators\RequiredValidator::class => RestValidators\RequiredValidator::class,
            \yii\validators\RegularExpressionValidator::class => RestValidators\RegularExpressionValidator::class,
            \yii\validators\UrlValidator::class => RestValidators\UrlValidator::class,
            \yii\validators\CompareValidator::class => RestValidators\CompareValidator::class,
            \yii\validators\IpValidator::class => RestValidators\IpValidator::class,
            \yii\validators\UniqueValidator::class => RestValidators\UniqueValidator::class,
            \yii\validators\ExistValidator::class => RestValidators\ExistValidator::class,
            Pagination::class => [
                'pageSizeParam' => 'perPage',
            ],
        ],
    ],
    'params' => $params,
];
