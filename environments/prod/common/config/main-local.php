<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

Yii::setAlias('@base.domain', '{{P_WEB_HOST}}');
Yii::setAlias('@rest.domain', '{{P_REST_WEB_HOST}}');
Yii::setAlias('@backend.domain', '{{P_BACKEND_WEB_HOST}}');

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host={{P_DB_HOST}};dbname={{P_DB_NAME}}',
            'username' => '{{P_DB_USERNAME}}',
            'password' => '{{P_DB_PASSWORD}}',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
