<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\components\rbac\controllers;

use common\components\rbac\rules\UserGroupRule;
use common\models\User;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class RbacController
 *
 * Contains users rules
 */
class RbacController extends Controller
{
    /**
     * Initialize rbac
     * Setup user rules
     */
    public function actionInit(): void
    {
        $authManager = \Yii::$app->authManager;

        // Create roles
        $guestRole = $authManager->createRole(User::ROLE_GUEST);
        $basicRole = $authManager->createRole(User::ROLE_BASIC);
        $advancedRole = $authManager->createRole(User::ROLE_ADVANCED);

        // Create simple, based on action{$NAME} permissions
        $basicPermission = $authManager->createPermission(User::ROLE_BASIC . '_permission');
        $advancedPermission = $authManager->createPermission(User::ROLE_ADVANCED . '_permission');

        // Add rule, based on UserExt->group === $user->group
        $userGroupRule = new UserGroupRule();
        $authManager->add($userGroupRule);

        // Add permissions in Yii::$app->authManager
        $authManager->add($basicPermission);
        $authManager->add($advancedPermission);

        // Add rule "UserGroupRule" in roles
        $guestRole->ruleName = $userGroupRule->name;
        $basicRole->ruleName = $userGroupRule->name;
        $advancedRole->ruleName = $userGroupRule->name;

        // Add roles in Yii::$app->authManager
        $authManager->add($guestRole);
        $authManager->add($basicRole);
        $authManager->add($advancedRole);

        $authManager->addChild($basicRole, $basicPermission);

        $authManager->addChild($advancedRole, $advancedPermission);
    }

    /**
     * Command check role by user id
     *
     * @param $userId
     */
    public function actionCheckRole(int $userId): void
    {
        echo $this->check($userId, User::ROLE_GUEST);
        echo $this->check($userId, User::ROLE_BASIC);
        echo $this->check($userId, User::ROLE_ADVANCED);
        echo $this->check($userId, 'unknown');
    }

    /**
     * @param int $userId
     * @param string $role
     * @return string
     */
    private function check(int $userId, string $role): string
    {
        return $this->ansiFormat($role, Console::FG_YELLOW, Console::BOLD)
            . ': '
            . (
                \Yii::$app->authManager->checkAccess($userId, $role)
                ? $this->ansiFormat('access', Console::FG_GREEN, Console::UNDERLINE)
                : $this->ansiFormat('denied', Console::FG_RED, Console::UNDERLINE)
            ) . PHP_EOL;
    }
}
