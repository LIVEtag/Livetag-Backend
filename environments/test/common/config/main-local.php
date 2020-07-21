<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\sentry\Component as SentryComponent;
use OlegTsvetkov\Yii2\Sentry\LogTarget as SentryLogTarget;
use yii\db\Connection;
use yii\log\FileTarget;
use yii\swiftmailer\Mailer;

Yii::setAlias('@base.domain', '{{YII_MAIN_DOMAIN}}');
Yii::setAlias('@rest.domain', '{{YII_REST_DOMAIN}}');
Yii::setAlias('@backend.domain', '{{YII_BACKEND_DOMAIN}}');

return [
    'bootstrap' => ['sentry', 'log'],
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host={{DB_HOST}};dbname={{DB_NAME}};port={{DB_PORT}}',
            'username' => '{{DB_USERNAME}}',
            'password' => '{{DB_PASSWORD}}',
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'sentry' => [
            'class' => SentryComponent::class,
            'dsn' => '{{SENTRY_DNS}}',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => SentryLogTarget::class,
                    'enabled' => filter_var('{{SENTRY_LOG_ENABLED}}', FILTER_VALIDATE_BOOLEAN),
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:4**',
                    ],
                ],
            ],
        ],
    ],
    'container' => [
        'singletons' => [
            \Faker\Generator::class => function () {
                $generator = \Faker\Factory::create('en_EN');
                $generator->addProvider(new \common\components\test\faker\IncrementalTimeProvider($generator));
                return $generator;
            },
        ]
    ],
];
