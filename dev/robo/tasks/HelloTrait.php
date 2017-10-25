<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace robo\tasks;

/**
 * Trait HelloTrait
 */
trait HelloTrait
{
    /**
     * @param string $world
     */
    public function hello($world = 'test')
    {
        /** @var $this \Robo\Tasks */
        $this->say("Hello, $world");
    }
}
