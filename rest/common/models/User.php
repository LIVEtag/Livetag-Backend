<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models;

use yii\web\User as BaseUser;
use rest\common\models\queries\User\UserQuery;
use yii\web\IdentityInterface;
use rest\common\services\AccessToken\AccessTokenSearchService;
use rest\common\models\views\AccessToken\AccessTokenInterface;

/**
 * User model
 *
 * @property AccessToken $accessToken
 */
class User extends BaseUser
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
     * @inheritdoc
     * @return UserQuery
     */
    public static function find()
    {
        return \Yii::createObject(UserQuery::class, [get_called_class()]);
    }

    /** @var AccessTokenInterface */
    protected $accessToken;

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @param string $token
     * @param null $type
     * @return IdentityInterface|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loginByAccessToken($token, $type = null)
    {
        $accessToken = AccessToken::find()
            ->byToken($token)
            ->valid()
            ->one();
        if ($accessToken) {
            $this->accessToken = $accessToken;
            /* @var $class IdentityInterface */
            $class = $this->identityClass;
            $identity = $class::findIdentity($accessToken->userId);
            if ($identity && $this->login($identity)) {
                return $identity;
            }
        }
        return null;
    }
}
