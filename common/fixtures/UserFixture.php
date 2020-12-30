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
    const SELLER = 2;
    const DELETED = 3;

    const DEFAULT_PASSWORD = 'Password_0';

    public $modelClass = User::class;
    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'authKey' => $this->security->generateRandomString(),
            'passwordHash' => $this->security->generatePasswordHash(self::DEFAULT_PASSWORD),
            'passwordResetToken' => null,
            'role' => User::ROLE_SELLER,
            'status' => User::STATUS_ACTIVE,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
            'email' => $this->generator->unique()->email,
        ];
    }
}