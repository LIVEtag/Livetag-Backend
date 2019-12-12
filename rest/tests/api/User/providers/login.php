<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use rest\components\validation\ErrorList;
use common\fixtures\UserFixture;
use common\models\User;

/** @var User $user */
$user = $I->grabFixture('users', UserFixture::USER);

return [
    'login' => [
        [
            'dataComment' => 'Check correct login',
            'request' => [
                'email' => $user->email,
                'password' => UserFixture::DEFAULT_PASSWORD,
            ],
            'response' => [
                'token' => 'string',
                'expiredAt' => 'integer'
            ]
        ]
    ],
    'validation' => [
        [
            'dataComment' => 'Check incorrect email is not allowed',
            'request' => [
                'email' => 'rand@test.com',
                'password' => 'password_0',
            ],
            'response' => [
                [
                    'field' => 'password',
                    'message' => 'Incorrect email address and/or password',
                    'code' => ErrorList::CREDENTIALS_INVALID,
                ],
            ]
        ],
        [
            'dataComment' => 'Check incorrect password is not allowed',
            'request' => [
                'email' => 'user@test.com',
                'password' => 'password_'
            ],
            'response' => [
                [
                    'field' => 'password',
                    'message' => 'Incorrect email address and/or password',
                    'code' => ErrorList::CREDENTIALS_INVALID,
                ],
            ]
        ],
        [
            'dataComment' => 'Check that required fields cannot be blank',
            'request' => [
                'email' => '',
                'password' => ''
            ],
            'response' => [
                [
                    'field' => 'password',
                    'message' => 'Password cannot be blank.',
                    'code' => ErrorList::REQUIRED_INVALID,
                ],
                [
                    'field' => 'email',
                    'message' => 'Email cannot be blank.',
                    'code' => ErrorList::REQUIRED_INVALID,
                ],
            ]
        ]
    ],
];
