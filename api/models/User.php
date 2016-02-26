<?php
namespace api\models;

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
        /** @var $accessToken \api\models\AccessToken */
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

        return $accessToken->getUser();
    }
}
