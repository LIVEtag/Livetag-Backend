<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models;

use common\models\User as CommonUser;
use rest\common\models\queries\User\UserQuery;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property AccessToken $accessToken
 */
class User extends CommonUser
{
    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'email',
        ];
    }

    /**
     * @return object|\yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return \Yii::createObject(UserQuery::class, [get_called_class()]);
    }

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
        /** @var $accessToken AccessToken */
        $accessToken = AccessToken::find()
            ->byToken($token)
            ->valid()
            ->one();

        if ($accessToken !== null) {
            $user = static::findOne(['id' => $accessToken->userId, 'status' => self::STATUS_ACTIVE]);
            if (!empty($user)) {
                // set current access token
                $user->accessToken = $accessToken;
            }

            return $user;
        }

        return null;
    }
}
