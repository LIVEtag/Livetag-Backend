<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\filters\RateLimiter\Rules;

use rest\components\filters\RateLimiter\MatchActionsTrait;
use rest\components\filters\RateLimiter\RateLimitRestrictionInterface;
use rest\components\filters\RateLimiter\RateLimitRuleInterface;
use yii\base\Component;
use yii\base\Action;
use yii\web\Request;
use yii\web\User as WebUser;

class RouteRateLimitRule extends Component implements RateLimitRuleInterface, RateLimitRestrictionInterface
{
    use MatchActionsTrait;

    /**
     * @var int Maximum number of allowed requests
     */
    public $maxCount;
    /**
     * @var int Size of the window in seconds
     */
    public $interval;

    /**
     * @inheritdoc
     */
    public function getRuleIdentity(Request $request, Action $action, WebUser $user): array
    {
        return ['route', $request->getMethod(), $action->getUniqueId()];
    }

    /**
     * @inheritdoc
     */
    public function match(Request $request, Action $action, WebUser $user): bool
    {
        return $this->matchAction($action);
    }

    /**
     * @inheritdoc
     */
    public function getRestriction(): RateLimitRestrictionInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMaxCount(): int
    {
        return $this->maxCount;
    }

    /**
     * @inheritdoc
     */
    public function getInterval(): int
    {
        return $this->interval;
    }
}
