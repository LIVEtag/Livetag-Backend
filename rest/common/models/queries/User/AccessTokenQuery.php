<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\queries\User;

use rest\common\models\AccessToken;
use yii\db\ActiveQuery;

/**
 * Class AccessTokenQuery
 */
class AccessTokenQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return AccessToken|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Find current access token by matching client
     *
     * @param string $userAgent
     * @param string $userIp
     * @return AccessTokenQuery
     */
    public function findCurrentToken($userAgent, $userIp)
    {
        return $this->where(
            [
                'and',
                'expiredAt > :expiredAt',
                'userAgent = :userAgent',
                'userIp = :userIp',
            ],
            [
                ':expiredAt' => time(),
                ':userAgent' => (string)$userAgent,
                ':userIp' => (string)$userIp,
            ]
        );
    }
}
