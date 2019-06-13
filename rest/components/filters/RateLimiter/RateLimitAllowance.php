<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\filters\RateLimiter;

use yii\base\BaseObject;

class RateLimitAllowance extends BaseObject
{
    /** @var int */
    public $quantity;

    /** @var int */
    public $timestamp;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'timestamp' => $this->timestamp,
        ];
    }
}
