<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Product\ListByShopAction;
use yii\helpers\ArrayHelper;
use rest\components\api\Controller;

/**
 * Class ProductController
 */
class ProductController extends Controller
{
    /**
     * Get list of products by shops
     */
    const ACTION_INDEX = 'index';
    
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
                            'actions' => [self::ACTION_INDEX],
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }
    
    /**
     * @return array
     */
    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                self::ACTION_INDEX => ListByShopAction::class,
            ]
        );
    }
}
