<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models;

use common\models\User as CommonUser;
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
            return $accessToken->getUser()->one();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'username',
            'email',
            'created_at',
        ];
    }
}
