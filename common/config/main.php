<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\centrifugo\Centrifugo;
use common\components\EventDispatcher;
use common\components\streaming\Vonage;
use common\components\validation\ErrorList;
use common\components\validation\ErrorListInterface;
use common\components\validation\validators as RestValidators;
use creocoder\flysystem\AwsS3Filesystem;
use notamedia\sentry\SentryTarget;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\log\FileTarget;
use yii\queue\file\Queue as FileQueue;
use yii\queue\LogBehavior;
use yii\queue\sqs\Queue as SqsQueue;
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
use yii\web\UrlManager;

Yii::setAlias('@base.domain', getenv('YII_MAIN_DOMAIN'));
Yii::setAlias('@rest.domain', getenv('YII_REST_DOMAIN'));
Yii::setAlias('@backend.domain', getenv('YII_BACKEND_DOMAIN'));
Yii::setAlias('@sdk.domain', getenv('SDK_DOMAIN'));

return [
    'name' => 'LiveTag',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'timeZone' => 'UTC',
    'bootstrap' => [
        'log',
        'queue',
        EventDispatcher::class,
    ],
    'components' => [
        'formatter' => [
            'dateFormat' => 'dd/MM//yyyy',
            'timeFormat' => 'HH:mm:ss',
            'datetimeFormat' => 'dd/MM/yyyy, HH:mm:ss',
            'timeZone' => 'Singapore',
        ],
        'queue' => getenv('USE_FILE_QUEUE') ?
            [
                'class' => FileQueue::class,
                'path' => '@common/queue-' . getenv('AMAZON_SQS_GENERAL') ?: 'general',
                'as log' => LogBehavior::class
            ] :
            [
                'class' => SqsQueue::class,
                'url' => 'https://sqs.' . (getenv('AMAZON_SQS_REGION') ?: 'ap-southeast-1') . '.amazonaws.com/'
                        . getenv('AMAZON_ACCOUNT') . '/' . ENV  . '-'. getenv('AMAZON_SQS_GENERAL') ?: 'general',
                'key' => getenv('AMAZON_ACCESS_KEY') ?: '',
                'secret' => getenv('AMAZON_SECRET_KEY') ?: '',
                'region' => getenv('AMAZON_SQS_REGION') ?: 'ap-southeast-1',
                'as log' => LogBehavior::class
            ],
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';port=' . getenv('DB_PORT') . '',
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8mb4',
        ],
        'fs' => [
            'class' => AwsS3Filesystem::class,
            'key' => getenv('AMAZON_ACCESS_KEY'),
            'secret' => getenv('AMAZON_SECRET_KEY'),
            'bucket' => getenv('AMAZON_S3_BUCKET'),
            'region' => getenv('AMAZON_S3_REGION'),
            'prefix' => getenv('AMAZON_S3_PREFIX'),
            'options' => ['ACL' => 'public-read'],
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
                    'levels' => YII_DEBUG ? ['error', 'warning', 'info'] : ['error', 'warning'],
                    'except' => [
                        'yii\db\*',
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
        'vonage' => [
            'class' => Vonage::class,
            'apiKey' => getenv('VONAGE_API_KEY'),
            'apiSecret' => getenv('VONAGE_API_SECRET'),
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
        ],
        'urlManagerRest' => [
            'class' => UrlManager::class,
            'baseUrl' => 'https://' . Yii::getAlias('@rest.domain') . '/rest',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'urlManagerBackend' => require __DIR__ . '/../../backend/config/urlManager.php',
        'urlManagerSDK' => [
            'class' => UrlManager::class,
            'baseUrl' => Yii::getAlias('@sdk.domain'),
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
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
