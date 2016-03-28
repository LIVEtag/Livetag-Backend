<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
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
                    'expired_at > :expired_at',
                    'user_agent = :user_agent',
                    'user_ip = :user_ip',
                ],
                [
                    ':expired_at' => time(),
                    ':user_agent' => (string) $userAgent,
                    ':user_ip' =>  (string) $userIp,
                ]
            );
    }
}
