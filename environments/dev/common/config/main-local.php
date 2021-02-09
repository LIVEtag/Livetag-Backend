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
