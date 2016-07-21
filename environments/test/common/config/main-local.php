<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

Yii::setAlias('@base.domain', '{{T_WEB_HOST}}');
Yii::setAlias('@rest.domain', '{{T_REST_WEB_HOST}}');
Yii::setAlias('@backend.domain', '{{T_BACKEND_WEB_HOST}}');

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host={{T_DB_HOST}};dbname={{T_DB_NAME}}',
            'username' => '{{T_DB_USERNAME}}',
            'password' => '{{T_DB_PASSWORD}}',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
