<?php

/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace backend\controllers;

use backend\components\Controller;
use backend\models\Product\Product;
use backend\models\Product\ProductForm;
use backend\models\Product\ProductOptionForm;
use backend\models\Product\ProductSearch;
use backend\models\Stream\StreamSession;
use backend\models\User\User;
use backend\models\Product\ProductsUploadForm;
use common\models\Model;
use Throwable;
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
     * @throws Throwable
     */
    public function actionIndex()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || !$user->isSeller || !$user->shop) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        //Product List
        $searchModel = new ProductSearch();
        $params = Yii::$app->request->queryParams;
        if ($user->isSeller) {
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($searchModel)) => ['shopId' => $user->shop->id]]);
        }
        $dataProvider = $searchModel->search($params);

        //Product upload Form
        $model = new ProductsUploadForm($user->shop);
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'The list of the products was added.');
            } else {
                Yii::$app->session->setFlash('error', $model->getErrorsAsString());
            }
        }

        $activeStreamSessionExists = StreamSession::activeExists($user->shop->id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'activeStreamSessionExists' => $activeStreamSessionExists
        ]);
    }

    /**
     * Add product
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionCreate()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || !$user->isSeller || !$user->shop) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $model = new ProductForm();
        $model->scenario = ProductForm::SCENARIO_CREATE;

        $params = Yii::$app->request->post();
        if ($params) { //shop and seller checked before
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($model)) => ['shopId' => $user->shop->id]]);
        }
        if ($model->load($params)) {
            $model->files = UploadedFile::getInstances($model, 'files');
            $model->productOptions = Model::createMultiple(ProductOptionForm::class);
            Model::loadMultiple($model->productOptions, $params);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Product is added to the list of products.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionUpdate(int $id)
    {
        $product = $this->findModel($id);
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || !$user->isSeller || !$user->shop) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $productOptions = [];
        foreach ($product->getOptions() as $option) {
            $modelProduct = new ProductOptionForm();
            $modelProduct->setAttributes($option);
            $productOptions[] = $modelProduct;
        }
        $model = new ProductForm($product, $productOptions);
        $params = Yii::$app->request->post();
        if ($params) { //shop and seller checked before
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($model)) => ['shopId' => $user->shop->id]]);
        }
        if ($model->load($params)) {
            $model->files = UploadedFile::getInstances($model, 'files');
            $model->productOptions = Model::createMultiple(ProductOptionForm::class);
            Model::loadMultiple($model->productOptions, $params);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Product is updated.');
                return $this->redirect(['view', 'id' => $product->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
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
