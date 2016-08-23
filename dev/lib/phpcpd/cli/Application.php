<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace dev\lib\phpcpd\cli;

use SebastianBergmann\PHPCPD\CLI\Application as BaseApplication;

/**
 * Class Application
 */
class Application extends BaseApplication
{
    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new Command();

        return $defaultCommands;
    }
}
