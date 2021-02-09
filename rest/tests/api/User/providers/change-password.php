<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\validation\ErrorList;
use common\fixtures\UserFixture;
use rest\tests\ApiTester;

/**
 * @var ApiTester $I
 */
$password = $I->generator->password(8, 15) . 'Pp1';

return [
    'update' => [
        [
            'dataComment' => 'Correct change password',
            'request' => [
                'password' => UserFixture::DEFAULT_PASSWORD,
                'newPassword' => $password,
                'confirmPassword' => $password,
            ],
            'response' => [
                'password' => UserFixture::DEFAULT_PASSWORD,
                'newPassword' => $password,
                'confirmPassword' => $password,
            ]
        ]
    ],
    'validation' => [
        [
            'dataComment' => 'Check that incorrect current password is not allowed',
            'request' => [
                'password' => $password,
                'newPassword' => $password . '2',
                'confirmPassword' => $password . '2',
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
            'dataComment' => 'Check that not same newPassword and confirmPassword are not allowed',
            'request' => [
                'password' => UserFixture::DEFAULT_PASSWORD,
                'newPassword' => $password,
                'confirmPassword' => $password . '1'
            ],
            'response' => [
                [
                    'field' => 'confirmPassword',
                    'message' => 'Confirm Password must be equal to "New Password".',
                    'code' => ErrorList::COMPARE_EQUAL,
                ],
            ]
        ],
        [
            'dataComment' => 'Check that newPassword cannot be the same as the current password',
            'request' => [
                'password' => UserFixture::DEFAULT_PASSWORD,
                'newPassword' => UserFixture::DEFAULT_PASSWORD,
                'confirmPassword' => UserFixture::DEFAULT_PASSWORD,
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
            'dataComment' => 'Check that required fields cannot be blank',
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
