<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\User;

/**
 * Class UserFixture
 */
class UserFixture extends ActiveFixture
{
    const ADMIN = 1;
    const SELLER_1 = 2;
    const SELLER_2 = 3;
    const SELLER_3 = 4;
    const DELETED = 5; //todo change to blocked
    const BUYER_1 = 6;
    const BUYER_2 = 7;

    const DEFAULT_PASSWORD = 'Password_0';

    public $modelClass = User::class;

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'email' => $this->generator->unique()->email,
            'uuid' => null,
            'authKey' => $this->security->generateRandomString(),
            'passwordHash' => $this->security->generatePasswordHash(self::DEFAULT_PASSWORD),
            'passwordResetToken' => null,
            'role' => User::ROLE_SELLER,
            'status' => User::STATUS_ACTIVE,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
