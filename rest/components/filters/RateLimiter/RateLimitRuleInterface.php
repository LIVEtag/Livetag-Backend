<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\filters\RateLimiter;

use yii\web\Request;
use yii\base\Action;
use yii\web\User as WebUser;

interface RateLimitRuleInterface
{
    /**
     * Return rate limit restrictions
     * @return RateLimitRestrictionInterface
     */
    public function getRestriction(): RateLimitRestrictionInterface;

    /**
     * Checks if rule can be applied
     * @param Request $request
     * @param Action $action
     * @param WebUser $user
     * @return bool
     */
    public function match(Request $request, Action $action, WebUser $user): bool;

    /**
     * Return rule identity to store each limit with unique key
     * @param Request $request
     * @param Action $action
     * @param WebUser $user
     * @return array
     */
    public function getRuleIdentity(Request $request, Action $action, WebUser $user): array;
}
