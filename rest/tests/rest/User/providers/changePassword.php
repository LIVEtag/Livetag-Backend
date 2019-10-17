<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use rest\components\validation\ErrorList;

return [
    'validation' => [
        [
            'goingTo' => 'Check that incorrect current password is not allowed',
            'request' => [
                'password' => 'password_1',
                'newPassword' => 'password_2',
                'confirmPassword' => 'password_2'
            ],
            'response' => [
                [
                    'field' => 'password',
                    'message' => 'Current password is wrong.',
                    'code' => ErrorList::CURRENT_PASSWORD_IS_WRONG,
                ],
            ]
        ],
        [
            'goingTo' => 'Check that not same newPassword and confirmPassword are not allowed',
            'request' => [
                'password' => 'password_0',
                'newPassword' => 'password_2',
                'confirmPassword' => 'password_3'
            ],
            'response' => [
                [
                    'field' => 'newPassword',
                    'message' => 'New Password must be equal to "Confirm Password".',
                    'code' => ErrorList::COMPARE_EQUAL,
                ],
            ]
        ],
        [
            'goingTo' => 'Check that newPassword cannot be the same as the current password',
            'request' => [
                'password' => 'password_0',
                'newPassword' => 'password_0',
                'confirmPassword' => 'password_0'
            ],
            'response' => [
                [
                    'field' => 'newPassword',
                    'message' => 'New password can not be the same as old password',
                    'code' => ErrorList::SAME_CURRENT_PASSWORD_AND_NEW_PASSWORD,
                ],
            ]
        ],
        [
            'goingTo' => 'Check that required fields cannot be blank',
            'request' => [
                'password' => '',
                'newPassword' => '',
                'confirmPassword' => ''
            ],
            'response' => [
                [
                    'field' => 'password',
                    'message' => 'Password cannot be blank.',
                    'code' => ErrorList::REQUIRED_INVALID,
                ],
                [
                    'field' => 'newPassword',
                    'message' => 'New Password cannot be blank.',
                    'code' => ErrorList::REQUIRED_INVALID,
                ],
                [
                    'field' => 'confirmPassword',
                    'message' => 'Confirm Password cannot be blank.',
                    'code' => ErrorList::REQUIRED_INVALID,
                ],
            ]
        ]
    ],
];
