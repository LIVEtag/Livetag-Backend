<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\db\Connection;
use yii\swiftmailer\Mailer;

Yii::setAlias('@base.domain', '{{WEB_HOST}}');
Yii::setAlias('@rest.domain', '{{REST_WEB_HOST}}');
Yii::setAlias('@backend.domain', '{{BACKEND_WEB_HOST}}');

return [
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host={{DB_HOST}};dbname={{DB_NAME}}',
            'username' => '{{DB_USERNAME}}',
            'password' => '{{DB_PASSWORD}}',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
