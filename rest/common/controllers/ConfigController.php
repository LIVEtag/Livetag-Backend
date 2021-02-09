<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Config\IndexAction;
use rest\components\api\Controller;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ConfigController
 */
class ConfigController extends Controller
{
    const ACTION_INDEX = 'index';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'except' => [self::ACTION_INDEX],
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [self::ACTION_INDEX],
                            'roles' => ['?'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                self::ACTION_INDEX => [
                    'class' => IndexAction::class,
                    'configPath' => Yii::getAlias('@v1/config/') . 'config.php',
                ],
            ]
        );
    }
}
