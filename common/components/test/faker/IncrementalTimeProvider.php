<?php
/**
 * Copyright Â© 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\test\faker;

use Faker\Generator;
use Faker\Provider\Base as BaseProvider;

class IncrementalTimeProvider extends BaseProvider
{
    /**
     * @var int
     */
    protected $incrementalTime;

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        $this->incrementalTime = time();
    }

    /**
     * @param int $value
     * @return void
     */
    public function setIncrementalTime(int $value)
    {
        $this->incrementalTime = $value;
    }

    /**
     * @return int
     */
    public function getIncrementalTime()
    {
        return $this->incrementalTime >= time() ? $this->incrementalTime : time();
    }

    /**
     * @param int $seconds
     * @return mixed
     */
    public function incrementalTime(int $seconds = 1)
    {
        $this->setIncrementalTime($this->getIncrementalTime() + $seconds);
        return $this->incrementalTime;
    }
}
