<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\modules\v1\controllers;

use rest\common\controllers\actions\Config\IndexAction;
use rest\common\controllers\ConfigController as CommonConfigController;

/**
 * Class ConfigController
 */
class ConfigController extends CommonConfigController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
                'configPath' => \Yii::getAlias('@v1/config/') . 'config.php',
            ],
        ];
    }
}
