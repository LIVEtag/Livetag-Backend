<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use rest\components\validation\ErrorList;

return [
    'validation' => [
        [
            'goingTo' => 'Check that email validation works',
            'request' => [
                'email' => 'rand',
                'password' => '',
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
            'goingTo' => 'Check that email must be unique',
            'request' => [
                'email' => 'user@test.com',
                'password' => '1234',
            ],
            'response' => [
                [
                    'field' => 'email',
                    'message' => 'Email "user@test.com" has already been taken.',
                    'code' => ErrorList::UNIQUE_INVALID,
                ],
            ]
        ],
        [
            'goingTo' => 'Check that required fields cannot be blank',
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
            'goingTo' => 'Check that password length validation works',
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
