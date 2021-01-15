<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\centrifugo\Centrifugo;
use common\components\streaming\Vonage;
use common\components\validation\ErrorList;
use common\components\validation\ErrorListInterface;
use common\components\validation\validators as RestValidators;
use notamedia\sentry\SentryTarget;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\log\FileTarget;
use yii\swiftmailer\Mailer;
use yii\validators\BooleanValidator;
use yii\validators\CompareValidator;
use yii\validators\DateValidator;
use yii\validators\EmailValidator;
use yii\validators\ExistValidator;
use yii\validators\FileValidator;
use yii\validators\ImageValidator;
use yii\validators\IpValidator;
use yii\validators\NumberValidator;
use yii\validators\RangeValidator;
use yii\validators\RegularExpressionValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\UniqueValidator;
use yii\validators\UrlValidator;

Yii::setAlias('@base.domain', getenv('YII_MAIN_DOMAIN'));
Yii::setAlias('@rest.domain', getenv('YII_REST_DOMAIN'));
Yii::setAlias('@backend.domain', getenv('YII_BACKEND_DOMAIN'));

return [
    'name' => 'LiveTag',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2). '/vendor',
    'timeZone' => 'UTC',
    'bootstrap' => ['log'],
    'components' => [
        'formatter' => [
            'dateFormat' => 'dd/MM//yyyy',
            'timeFormat' => 'HH:mm:ss',
            'datetimeFormat' => 'dd/MM/yyyy, HH:mm:ss',
            'timeZone' => 'Singapore',
        ],
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';port=' . getenv('DB_PORT') . '',
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => Swift_SmtpTransport::class,
                'host' => getenv('MAIL_HOST'),
                'username' => getenv('MAIL_USERNAME'),
                'password' => getenv('MAIL_PASSWORD'),
                'port' => getenv('MAIL_PORT'),
                'encryption' => getenv('MAIL_ENCRYPTION'),
            ],
            'useFileTransport' => filter_var(getenv('MAIL_USEFILETRANSPORT'), FILTER_VALIDATE_BOOLEAN),
            'fileTransportPath' => '@common/runtime/mail',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => SentryTarget::class,
                    'dsn' => getenv('SENTRY_DSN'),
                    'enabled' => filter_var(getenv('SENTRY_LOG_ENABLED'), FILTER_VALIDATE_BOOLEAN),
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:4**',
                    ],
                    // Write the context information (the default is true):
                    'context' => true,
                ],
            ],
        ],
        'centrifugo' => [
            'class' => Centrifugo::class,
            'host' => getenv('CENTRIFUGO_HOST'),
            'ws' => getenv('CENTRIFUGO_WEB_SOCKET'),
            'secret' => getenv('CENTRIFUGO_TOKEN_HMAC_SECRET_KEY'),
            'apiKey' => getenv('CENTRIFUGO_API_KEY'),
        ],
        // If the project uses a load balancer, the file cache must be replaced (redis, memcached etc.)
        'cache' => [
            'class' => FileCache::class,
            'cachePath' => Yii::getAlias('@rest') . '/runtime/cache'//to store cache it one place for invalidation
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
        ],
        'vonage' => [
            'class' => Vonage::class,
            'apiKey' => getenv('VONAGE_API_KEY'),
            'apiSecret' => getenv('VONAGE_API_SECRET'),
        ],
        'urlManagerBackend' => require __DIR__ . '/../../backend/config/urlManager.php',
    ],
    'container' => [
        'singletons' => [
            ErrorListInterface::class => ErrorList::class,
        ],
        'definitions' => [
            StringValidator::class => RestValidators\StringValidator::class,
            EmailValidator::class => RestValidators\EmailValidator::class,
            FileValidator::class => RestValidators\FileValidator::class,
            ImageValidator::class => RestValidators\ImageValidator::class,
            BooleanValidator::class => RestValidators\BooleanValidator::class,
            NumberValidator::class => RestValidators\NumberValidator::class,
            DateValidator::class => RestValidators\DateValidator::class,
            RangeValidator::class => RestValidators\RangeValidator::class,
            RequiredValidator::class => RestValidators\RequiredValidator::class,
            RegularExpressionValidator::class => RestValidators\RegularExpressionValidator::class,
            UrlValidator::class => RestValidators\UrlValidator::class,
            CompareValidator::class => RestValidators\CompareValidator::class,
            IpValidator::class => RestValidators\IpValidator::class,
            UniqueValidator::class => RestValidators\UniqueValidator::class,
            ExistValidator::class => RestValidators\ExistValidator::class,
        ],
    ],
];
