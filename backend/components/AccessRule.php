<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace backend\components;

use Closure;
use yii\base\InvalidConfigException;
use yii\filters\AccessRule as BaseAccessRule;

class AccessRule extends BaseAccessRule
{

    /**
     * @inheritdoc
     *
     * Additional role check in AccessControl
     * ```php
     * 'access' => [
     *    'class' => AccessControl::class,
     *    'rules' => [
     *        [
     *            'allow' => true,
     *            'actions' => ['current'],
     *            'roles' => [User::ROLE_SELLER, User::ROLE_ADMIN],
     *        ]
     *    ]
     * ]
     * ```
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function matchRole($user)
    {
        $items = empty($this->roles) ? [] : $this->roles;

        if (!empty($this->permissions)) {
            $items = array_merge($items, $this->permissions);
        }

        if (empty($items)) {
            return true;
        }

        if ($user === false) {
            throw new InvalidConfigException('The user application component must be available to specify roles in AccessRule.');
        }

        if (!$user->getIsGuest() &&
            isset($user->identity->role) &&
            in_array($user->identity->role, $items)) {
            return true;
        }

        foreach ($items as $item) {
            if ($item === '?' && $user->getIsGuest()) {
                return true;
            } elseif ($item === '@' && !$user->getIsGuest()) {
                return true;
            }
            $roleParams = $this->roleParams instanceof Closure ? call_user_func($this->roleParams, $this) : $this->roleParams;
            if ($user->can($item, $roleParams)) {
                return true;
            }
        }

        return false;
    }
}
