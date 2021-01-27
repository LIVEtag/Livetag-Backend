<?php

/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace backend\controllers;

use backend\models\Product\Product;
use backend\models\User\User;
use kartik\grid\EditableColumnAction;
use Yii;
use backend\models\Product\ProductSearch;
use yii\helpers\ArrayHelper;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * Editable Post Action
     */
    const ACTION_EDITABLE_STATUS = 'editable-status';
    
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
                            'roles' => [User::ROLE_SELLER],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ]
            ]
        );
    }
    
    public function actions()
    {
        return [
            self::ACTION_EDITABLE_STATUS => [
                'class' => EditableColumnAction::class,
                'modelClass' => Product::class
            ],
        ];
    }
    
    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $statuses = Product::STATUSES;
        $statusesAvailable = [];
        array_filter($statuses, static function ($value) use ($statuses, &$statusesAvailable) {
            if (\is_array($statuses) && !empty($statuses)) {
                $key = array_flip($statuses)[$value];
                if (Product::STATUS_DELETED !== $key) {
                    $statusesAvailable[$key] = $value;
                }
            }
        });
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statusesAvailable' => $statusesAvailable
        ]);
    }
    
    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Product::findOne($id);
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
