<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\AccessToken;

/**
 * Class AccessTokenFixture
 */
class AccessTokenFixture extends ActiveFixture
{
    const ADMIN = 1;
    const SELLER_1 = 2;
    const SELLER_2 = 3;
    const DELETED = 4;

    public $modelClass = AccessToken::class;
    public $depends = [UserFixture::class];

    /** @inheritdoc */
    public $requiredAttributes = ['userId'];

    /**
     * 30 days
     */
    const REMEMBER_ME_TIME = 2592000;

    /** @inheritdoc */
    protected function getTemplate(): array
    {
        return [
            'token' => $this->generator->uuid,
            'userIp' => $this->generator->ipv4,
            'userAgent' => $this->generator->userAgent,
            'createdAt' => $this->generator->incrementalTime,
            'expiredAt' => time() + self::REMEMBER_ME_TIME,
        ];
    }
}
