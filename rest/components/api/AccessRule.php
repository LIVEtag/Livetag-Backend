<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api;

use yii\filters\AccessRule as BaseAccessRule;

class AccessRule extends BaseAccessRule
{
    /**
     * @inheritdoc
     *
     * Additional role check in AccessControl
     * ```php
     *'access' => [
     *    'class' => AccessControl::class,
     *    'rules' => [
     *        [
     *            'allow' => true,
     *            'actions' => ['current'],
     *            'roles' => [User::ROLE_BASIC, User::ROLE_ADVANCED],
     *        ]
     *    ]
     *]
     * ```
     */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }
        if ($user === false) {
            throw new InvalidConfigException('The user application component must be available to specify roles in AccessRule.');
        }
        
        if (!$user->getIsGuest() && in_array($user->identity->role, $this->roles)) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
            } else {
                if (!isset($roleParams)) {
                    $roleParams = $this->roleParams instanceof Closure ? call_user_func($this->roleParams, $this) : $this->roleParams;
                }
                if ($user->can($role, $roleParams)) {
                    return true;
                }
            }
        }

        return false;
    }
}
