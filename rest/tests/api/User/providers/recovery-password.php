<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\fixtures\UserFixture;
use common\models\User;
use rest\tests\ApiTester;

/** @var ApiTester $I */
$email = $I->generator->unique()->email;
/** @var User $user */
$user = $I->grabFixture('users', UserFixture::DELETED);

return [
    'validation' => [
        [
            'dataComment' => 'Check not exist email',
            'request' => [
                'email' => $email,
            ],
        ],
        [
            'dataComment' => 'Check incorrect email is not allowed',
            'request' => [
                'email' => str_repeat('x', 5),
            ],
        ],
        [
            'dataComment' => 'Check that required fields cannot be blank',
            'request' => [
                'email' => '',
            ],
        ],
        [
            'dataComment' => 'Check that deleted user cant be recovered',
            'request' => [
                'email' => $user->email,
            ]
        ],
    ],
];
