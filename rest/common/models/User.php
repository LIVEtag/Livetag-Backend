<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace rest\common\models;

use common\models\User as CommonUser;
use rest\common\models\queries\User\AccessTokenQuery;
use rest\common\models\queries\User\UserQuery;
use yii\web\IdentityInterface;

/**
 * User model
 */
class User extends CommonUser implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var $accessToken AccessToken */
        $accessToken = AccessToken::find()->findCurrentToken(
            \Yii::$app->request->getUserAgent(),
            \Yii::$app->request->getUserIP()
        )->andWhere(
            [
                'and',
                'token = :token',
            ],
            [
                ':token' => $token,
            ]
        )->one();

        if ($accessToken !== null) {
            return $accessToken->getUser()
                ->andWhere('status = :status', [':status' => self::STATUS_ACTIVE])
                ->one();
        }

        return null;
    }

    /**
     * @return null|AccessTokenQuery
     */
    public function getAccessToken()
    {
        return $this->hasOne(AccessToken::class, ['userId' => 'id'])
            ->where('expiredAt > :expiredAt', [':expiredAt' => time()]);
    }

    /**
     * @return null|AccessTokenQuery
     */
    public function getAccessTokens()
    {
        return $this->hasMany(AccessToken::class, ['userId' => 'id'])
            ->where('expiredAt > :expiredAt', [':expiredAt' => time()]);
    }

    /**
     * @inheritdoc
     * @return UserQuery
     */
    public static function find()
    {
        return \Yii::createObject(UserQuery::class, [get_called_class()]);
    }

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
}
