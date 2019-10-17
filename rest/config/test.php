<?php

use rest\tests\Message;

return [
    'id' => 'app-rest-tests',
    'components' => [
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
    'container' => [
        'definitions' => [
            \yii\swiftmailer\Message::class => Message::class,
        ],
    ]
];
