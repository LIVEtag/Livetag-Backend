<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models;

use common\models\User as CommonUser;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property AccessToken $accessToken
 */
class User extends CommonUser
{
    /** @var AccessToken */
    protected $accessToken;

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @param $token
     * @param null $type
     * @return User|void|IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        switch ($type) {
            case HttpBasicAuth::class:
                return self::getOrCreateBuyer($token);
            case HttpBearerAuth::class:
                /** @var $accessToken AccessToken */
                $accessToken = AccessToken::find()
                    ->byToken($token)
                    ->valid()
                    ->one();

                if ($accessToken !== null) {
                    $user = static::findOne(['id' => $accessToken->userId, 'status' => self::STATUS_ACTIVE]);
                    if (!empty($user)) {
                        $user->accessToken = $accessToken; // set current access token
                    }
                    return $user;
                }
        }
        return null;
    }

    /**
     * Get existing Buyer record or create new one
     * @param string $uuid
     * @return self|null
     */
    public static function getOrCreateBuyer($uuid): ?self
    {
        $user = self::find()
            ->byRole(self::ROLE_BUYER)
            ->byUuid($uuid)
            ->one();
        if ($user) {
            return $user->status == self::STATUS_ACTIVE ? $user : null;
        }
        $user = new self([
            'uuid' => $uuid,
            'role' => self::ROLE_BUYER
        ]);
        return $user->save() ? $user : null;
    }
}
