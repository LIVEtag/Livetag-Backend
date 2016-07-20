<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

Yii::setAlias('@base.domain', '{{WEB_HOST}}');
Yii::setAlias('@rest.domain', '{{REST_WEB_HOST}}');
Yii::setAlias('@backend.domain', '{{BACKEND_WEB_HOST}}');

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host={{L_DB_HOST}};dbname={{L_DB_NAME}}',
            'username' => '{{L_DB_USERNAME}}',
            'password' => '{{L_DB_PASSWORD}}',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
