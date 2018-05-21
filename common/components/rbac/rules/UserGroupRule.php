<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\components\rbac\rules;

use common\models\User;
use yii\rbac\Rule;

/**
 * Class UserGroupRule
 */
class UserGroupRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'userGroup';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if ($user === null) {
            return false;
        }

        $user = User::findIdentity((int) $user);

        /** @var User $user */
        if ($user !== null) {
            $role = $user->role;
            if ($item->name === User::ROLE_BASIC) {
                return $role === User::ROLE_BASIC || $role === User::ROLE_ADVANCED;
            } else if ($item->name === User::ROLE_ADVANCED) {
                return $role === User::ROLE_ADVANCED;
            }
        }

        return false;
    }
}
