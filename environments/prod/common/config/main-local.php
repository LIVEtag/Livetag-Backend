<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\db\Connection;
use yii\swiftmailer\Mailer;

Yii::setAlias('@base.domain', '{{MAIN_DOMAIN}}');
Yii::setAlias('@rest.domain', '{{REST_DOMAIN}}');
Yii::setAlias('@backend.domain', '{{BACKEND_DOMAIN}}');

return [
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host={{DB_HOST}};dbname={{DB_NAME}};port={{DB_PORT}}',
            'username' => '{{DB_USERNAME}}',
            'password' => '{{DB_PASSWORD}}',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@common/mail',
        ],
    ],
];
