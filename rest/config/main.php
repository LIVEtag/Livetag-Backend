<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use rest\common\controllers\ShopController;
use rest\common\models\User as RestUser;
use rest\components\api\ErrorHandler;
use rest\components\api\UrlRule;
use rest\modules\swagger\Module as SwaggerModule;
use rest\modules\v1\controllers\CentrifugoController;
use rest\modules\v1\controllers\ConfigController;
use rest\modules\v1\controllers\ProductController;
use rest\modules\v1\controllers\StreamSessionController;
use rest\modules\v1\Module as V1Module;
use yii\data\Pagination;
use yii\web\JsonParser;
use yii\web\Response;
use yii\web\User;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'rest-api',
    'basePath' => dirname(__DIR__),
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
            'class' => User::class,
            'identityClass' => RestUser::class,
            'enableSession' => false,
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
                        'GET validate-password-token/<token:>' => 'validate-password-token',
                        'OPTIONS validate-password-token/{token}' => 'options',
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
                    'controller' => ['v1/config'],
                    'pluralize' => false,
                    'only' => [
                        ConfigController::ACTION_INDEX,
                        ConfigController::ACTION_OPTIONS
                    ]
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => ['v1/stream-session'],
                    'pluralize' => false,
                    'patterns' => [
                        'POST' => StreamSessionController::ACTION_CREATE,
                        'OPTIONS' => StreamSessionController::ACTION_OPTIONS,
                        'GET {id}' => StreamSessionController::ACTION_VIEW,
                        'POST {id}' => StreamSessionController::ACTION_START,
                        'DELETE {id}' => StreamSessionController::ACTION_STOP,
                        'OPTIONS {id}' => StreamSessionController::ACTION_OPTIONS,
                    ],
                    'extraPatterns' => [
                        'GET {id}/token' => StreamSessionController::ACTION_TOKEN,
                        'OPTIONS {id}/token' => StreamSessionController::ACTION_OPTIONS,
                        'GET {id}/product' => StreamSessionController::ACTION_PRODUCTS,
                        'OPTIONS {id}/product' => StreamSessionController::ACTION_OPTIONS,
                    ]
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/shop' => 'v1/stream-session'
                    ],
                    'pluralize' => false,
                    'patterns' => [
                        'GET {slug}/stream-session/current' => StreamSessionController::ACTION_CURRENT,
                        'OPTIONS {slug}/stream-session/current' => StreamSessionController::ACTION_OPTIONS,
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => ['v1/centrifugo'],
                    'pluralize' => false,
                    'patterns' => [
                        'POST sign' => CentrifugoController::ACTION_SIGN,
                        'OPTIONS sign' => CentrifugoController::ACTION_OPTIONS,
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/shop' => 'v1/product'
                    ],
                    'pluralize' => false,
                    'patterns' => [
                        'GET {slug}/product' => ProductController::ACTION_INDEX,
                        'OPTIONS {slug}/product' => ProductController::ACTION_OPTIONS,
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/shop',
                    ],
                    'pluralize' => false,
                    'patterns' => [
                        'GET <id:\w+>' => ShopController::ACTION_VIEW,
                        'OPTIONS <id:\w+>' => ShopController::ACTION_OPTIONS,
                    ],
                ],
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            Pagination::class => [
                'pageSizeParam' => 'perPage',
            ],
        ],
    ],
    'params' => $params,
];
