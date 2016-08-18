<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
            'baseUrl' => '',
            'csrfCookie' => [
                'path' => '/'
            ],
        ],
        'user' => [
            'identityCookie' => [
                'path'=>'/'
            ]
        ],
    ],
];

return $config;
