<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\gii\Module as GiiModule;
use rest\generators\crud\Generator;

return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => [
            'class' => GiiModule::class,
            'allowedIPs' => ['*'],
            'generators' => [
                'restcrud' => [
                    'class' => Generator::class,
                    'templates' => [
                        'default' => '@rest/generators/crud/template',
                    ]
                ]
            ],
        ]
    ],
];
