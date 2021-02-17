<?php

/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace backend\controllers;

use backend\components\Controller;
use backend\models\Product\Product;
use backend\models\Product\ProductSearch;
use backend\models\StreamSessionProduct\StreamSessionProduct;
use backend\models\User\User;
use common\models\forms\Product\ProductsUploadForm;
use kartik\grid\EditableColumnAction;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
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

    /**
     * Lists all Product models.
     * @return mixed
     * @throws \Throwable
     */
    public function actionIndex()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $searchModel = new ProductSearch();
        $params = Yii::$app->request->queryParams;
        $shopId = $user->shop->id;
        if ($user->isSeller) {
            $params = ArrayHelper::merge($params, [StringHelper::basename(\get_class($searchModel)) => ['shopId' => $shopId]]);
        }
        
        $dataProvider = $searchModel->search($params);
        $isProductsExists = Product::find()->byShop($shopId)->exists();
        $model = new ProductsUploadForm();
        
        if ($shopId && $model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->validate() && $model->save($shopId)) {
                Yii::$app->session->setFlash('success', 'The list of the products was added.');
            } else {
                Yii::$app->session->setFlash('error', $model->getModelErrors());
            }
            return $this->redirect(['product/index']);
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'isProductsExists' => $isProductsExists,
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
