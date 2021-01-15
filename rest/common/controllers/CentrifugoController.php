<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers;

use rest\common\controllers\actions\Centrifugo\SignAction;
use rest\components\api\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class ProfileController
 * @package rest\common\controllers
 */
class CentrifugoController extends Controller
{
    const ACTION_SIGN = 'sign';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_SIGN,
                            ],
                            'roles' => ['@'],
                        ],
                    ],
                ]
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
                self::ACTION_SIGN => SignAction::class,
            ]
        );
    }
}
