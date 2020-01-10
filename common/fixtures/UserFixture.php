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
    public const USER = 1;
    public const DELETED = 2;

    public const DEFAULT_PASSWORD = 'password_0';

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
            'role' => User::ROLE_BASIC,
            'status' => User::STATUS_ACTIVE,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
            'email' => $this->generator->unique()->email,
        ];
    }
}