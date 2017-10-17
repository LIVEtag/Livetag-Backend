<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\chat;

use yii\base\Module as BaseModule;
use yii\helpers\ArrayHelper;

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
        $config = ArrayHelper::merge(
            require(__DIR__ . '/config/main.php'),
            require(__DIR__ . '/config/main-local.php')
        );
        \Yii::configure($this, $config);
    }
}
