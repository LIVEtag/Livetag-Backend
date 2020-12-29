<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\validation\ErrorList;
use common\fixtures\UserFixture;
use common\models\User;
use rest\tests\ApiTester;

/**
 * @var ApiTester $I
 */
/** @var User $user */
$user = $I->grabFixture('users', UserFixture::SELLER);
$email = $I->generator->unique()->email;
$password = $I->generator->password(8, 15);

return [
    'create' => [
        [
            'dataComment' => 'Correct register',
            'request' => [
                'email' => $email,
                'password' => $password,
            ],
            'response' => [
                'token' => 'string',
                'expiredAt' => 'integer'
            ]
        ]
    ],
    'validation' => [
        [
            'dataComment' => 'Check that email validation works',
            'request' => [
                'email' => 'wrongEmail',
                'password' => $password,
            ],
            'response' => [
                [
                    'field' => 'email',
                    'message' => 'Email is not a valid email address.',
                    'code' => ErrorList::EMAIL_INVALID,
                ],
            ]
        ],
        [
            'dataComment' => 'Check that email must be unique',
            'request' => [
                'email' => $user->email,
                'password' => UserFixture::DEFAULT_PASSWORD,
            ],
            'response' => [
                [
                    'field' => 'email',
                    'message' => "Email \"{$user->email}\" has already been taken.",
                    'code' => ErrorList::UNIQUE_INVALID,
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
        ],
        [
            'dataComment' => 'Check that password length validation works',
            'request' => [
                'password' => 1234
            ],
            'response' => [
                [
                    'field' => 'password',
                    'message' => 'Password should contain at least 6 character(s).',
                    'code' => ErrorList::STRING_TOO_SHORT,
                ],
            ]
        ]
    ],
];
