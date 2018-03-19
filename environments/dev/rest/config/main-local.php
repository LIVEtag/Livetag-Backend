<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\authclient\clients\Facebook;
use yii\authclient\clients\Google;
use yii\authclient\clients\LinkedIn;
use yii\authclient\clients\Twitter;
use yii\authclient\Collection;
use yii\debug\Module as DebugModule;
use yii\gii\Module as GiiModule;

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
            'baseUrl' => '',
        ],
        'authClientCollection' => [
            'class' => Collection::class,
            'clients' => [
                'google' => [
                    'class' => Google::class,
                    'validateAuthState' => false,
                    'scope' => 'email',
                    'clientId' => '{{GOOGLE_CLIENT_ID}}',
                    'clientSecret' => '{{GOOGLE_CLIENT_SECRET}}',
                ],
                'facebook' => [
                    'class' => Facebook::class,
                    'validateAuthState' => false,
                    'attributeNames' => [
                        'email'
                    ],
                    'clientId' => '{{FACEBOOK_CLIENT_ID}}',
                    'clientSecret' => '{{FACEBOOK_CLIENT_SECRET}}',
                ],
                'twitter' => [
                    'class' => Twitter::class,
                    'attributeParams' => [
                        'include_email' => 'true',
                    ],
                    'consumerKey' => '{{TWITTER_CONSUMER_KEY}}',
                    'consumerSecret' => '{{TWITTER_CONSUMER_SECRET}}',
                ],
                'linkedin' => [
                    'class' => LinkedIn::class,
                    'validateAuthState' => false,
                    'attributeNames' => [
                        'email-address',
                    ],
                    'consumerKey' => '{{LINKEDIN_CLIENT_ID}}',
                    'consumerSecret' => '{{LINKEDIN_CLIENT_SECRET}}',
                ]
            ],
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => DebugModule::class,
        'dataPath' => '@backend/runtime/debug',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => GiiModule::class,
    ];
}

return $config;
