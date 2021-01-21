<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models;

use common\models\User as CommonUser;
use rest\common\models\User\Buyer;
use rest\common\models\User\Seller;
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
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        switch ($row['role']) {
            case self::ROLE_SELLER:
                return new Seller();
            case self::ROLE_BUYER:
                return new Buyer();
            default:
                return new self;
        }
    }

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
                return Buyer::getOrCreate($token);
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
}
