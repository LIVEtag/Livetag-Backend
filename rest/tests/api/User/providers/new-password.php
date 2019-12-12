<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\fixtures\UserFixture;
use common\models\User;

/** @var UserFixture $userFixture */
$userFixture = \Yii::createObject(UserFixture::class);

/** @var User $userWithExpiredResetToken */
$userWithExpiredResetToken = $userFixture->createModel([
    'passwordResetToken' => \Yii::$app->security->generateRandomString() . '_' . strtotime('-2 days')
]);

return [
    'tokenValidation' => [
        [
            'dataComment' => 'Reset token cannot be blank',
            'request' => [
                'resetToken' => '',
            ],
        ],
        [
            'dataComment' => 'expiredTokenCantBeRecovered',
            'request' => [
                'resetToken' => $userWithExpiredResetToken->passwordResetToken,
                'password' => UserFixture::DEFAULT_PASSWORD,
                'confirmPassword' => UserFixture::DEFAULT_PASSWORD,
            ],
        ],
    ],
];
