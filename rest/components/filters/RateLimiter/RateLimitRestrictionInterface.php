<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\filters\RateLimiter;

interface RateLimitRestrictionInterface
{
    /**
     * Return the maximum number of allowed request
     * @return int
     */
    public function getMaxCount(): int;

    /**
     * Return size of the window in seconds
     * @return int
     */
    public function getInterval(): int;
}
