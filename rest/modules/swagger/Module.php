<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\swagger;

use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

/**
 * Class Module
 */
class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::configure($this, require(__DIR__ . '/config/main.php'));
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        //
    }
}
