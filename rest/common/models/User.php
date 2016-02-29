<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models;

use common\models\User as CommonUser;
use rest\common\models\queries\User\UserQuery;
use Yii;
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
        $accessToken = AccessToken::find()
            ->where(
                [
                    'and',
                    'token = :token',
                    'expired_at > :expired_at'
                ],
                [
                    ':token' => $token,
                    ':expired_at' => time()
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
     * @inheritdoc
     * @return UserQuery
     */
    public static function find()
    {
        return Yii::createObject(UserQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'username',
            'email',
        ];
    }
}
