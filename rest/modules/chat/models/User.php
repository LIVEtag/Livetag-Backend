<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace rest\modules\chat\models;

use rest\common\models\User as RestUser;
use yii\web\IdentityInterface;

/**
 * User model
 */
class User extends RestUser implements IdentityInterface
{

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'name'
        ];
    }

    /**
     * get user name (for chat display)
     */
    public function getName(): string
    {
        //TODO: replace with name or username if User model will contain this fields
        return $this->email ? substr($this->email, 0, strpos($this->email, '@')) : 'Unknown';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        $user = parent::findIdentityByAccessToken($token, $type = null);
        if (!$user) {
            return null;
        }
        return self::instantiateClass($user);
    }

    /**
     * Need to create separate class, but no access to $accessToken->getUser()
     * There SHOULD return object of class, that in 'identityClass' of user component
     *
     * @param RestUser $user
     * @return \self
     */
    public static function instantiateClass(RestUser $user): User
    {
        $child = new self();
        $child->setAttributes($user->getAttributes(), false);
        return $child;
    }
}
