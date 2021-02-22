<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Shop\ProductsAction;
use rest\common\models\Shop\Shop;
use rest\common\models\User;
use rest\components\api\ActiveController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class ShopController
 */
class ShopController extends ActiveController
{
    const ACTION_PRODUCTS = 'products';

    /**
     * @var string
     */
    public $modelClass = Shop::class;

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
                                self::ACTION_VIEW,
                                self::ACTION_PRODUCTS
                            ],
                            'roles' => [User::ROLE_BUYER, USER::ROLE_SELLER],
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
                self::ACTION_VIEW => [
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_PRODUCTS => [
                    'class' => ProductsAction::class,
                    'modelClass' => $this->modelClass,
                    'findModel' => [$this, 'findModel'],
                ]
            ]
        );
    }

    /**
     * @param $uri
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function findModel($uri)
    {
        $model = Shop::find()->byUri($uri)->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException("Shop not found by uri: $uri");
    }
}
