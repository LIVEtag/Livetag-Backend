<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use common\components\test\faker\IncrementalTimeProvider;
use Faker\Factory;
use Faker\Generator;

return [
    'bootstrap' => ['log'],
    'components' => [
        'mailer' => [
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => filter_var(getenv('MAIL_USEFILETRANSPORT'), FILTER_VALIDATE_BOOLEAN),
        ],
    ],
    'container' => [
        'singletons' => [
            Generator::class => function () {
                $generator = Factory::create('en_EN');
                $generator->addProvider(new IncrementalTimeProvider($generator));
                return $generator;
            },
        ]
    ],
];
