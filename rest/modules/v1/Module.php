<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\v1;

use yii\base\Module as BaseModule;

/**
 * Class Module
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::configure($this, require(__DIR__ . '/config/main.php'));
    }
}
