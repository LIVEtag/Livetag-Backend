<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\filters\RateLimiter;

use yii\base\Action;

trait MatchActionsTrait
{
    /**
     * @var array list of action IDs that this rule applies to
     */
    public $actions = [];

    /**
     * @param Action $action the action
     * @return bool whether the rule applies to the action
     */
    protected function matchAction($action): bool
    {
        return empty($this->actions) || in_array($action->id, $this->actions, true);
    }
}
