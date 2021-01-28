<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers\actions\Config;

use yii\base\Action;
use yii\web\ServerErrorHttpException;

/**
 * Class IndexAction
 */
class IndexAction extends Action
{
    /**
     * @var string Path to config file
     * You should provide its own config file path for each of api version (/rest/v1, /rest/v2, etc)
     */
    public $configPath;

    /**
     * @return mixed
     * @throws ServerErrorHttpException
     */
    public function run()
    {
        if (empty($this->configPath) || !file_exists($this->configPath)) {
            throw new ServerErrorHttpException('Config file was not set correctly');
        }

        $config = require $this->configPath;
        return $config;
    }
}
