<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\streaming\Vonage;
use common\components\validation\ErrorList;
use common\components\validation\ErrorListInterface;
use common\components\validation\validators as RestValidators;
use notamedia\sentry\SentryTarget;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\log\FileTarget;
use yii\swiftmailer\Mailer;

Yii::setAlias('@base.domain', getenv('YII_MAIN_DOMAIN'));
Yii::setAlias('@rest.domain', getenv('YII_REST_DOMAIN'));
Yii::setAlias('@backend.domain', getenv('YII_BACKEND_DOMAIN'));

return [
    'name' => 'LiveTag',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'UTC',
    'bootstrap' => ['log'],
    'components' => [
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
    ],
    'container' => [
        'singletons' => [
            ErrorListInterface::class => ErrorList::class,
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
        ],
    ],
];
