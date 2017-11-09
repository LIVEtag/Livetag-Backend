<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
Yii::setAlias('@chat', dirname(__DIR__));

use rest\modules\chat\models\User as ChatUser;
use yii\web\User as WebUser;

return [
    'components' => [
        'user' => [
            'class' => WebUser::class,
            'identityClass' => ChatUser::class,
        ]
    ]
];
