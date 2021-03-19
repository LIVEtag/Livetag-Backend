<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\controllers;

use backend\components\Controller;
use backend\models\Shop\Shop;
use backend\models\Shop\ShopSearch;
use backend\models\User\User;
use backend\models\User\UserSearch;
use common\helpers\LogHelper;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class ShopController extends Controller
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
                            'actions' => ['index', 'view', 'create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => [User::ROLE_ADMIN],
                        ],
                        [
                            'actions' => ['my', 'update-my'],
                            'allow' => true,
                            'roles' => [User::ROLE_SELLER],
                        ],
                        [
                            'actions' => ['delete-logo'],
                            'allow' => true,
                            'roles' => [User::ROLE_ADMIN, User::ROLE_SELLER],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-logo' => ['POST'],
                    ],
                ]
            ]
        );
    }

    /**
     * Lists all Shop models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ShopSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shop model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $model = $this->findModel($id);
        $userSearchModel = new UserSearch();

        //modify params for search models and pagination
        $params = ArrayHelper::merge(
            Yii::$app->request->queryParams,
            [
                StringHelper::basename(get_class($userSearchModel)) => ['shopId' => $model->id],
                'pageSize' => 10
            ]
        );
        $userDataProvider = $userSearchModel->search($params);
        return $this->render('view', [
            'model' => $model,
            'userSearchModel' => $userSearchModel,
            'userDataProvider' => $userDataProvider,
        ]);
    }

    /**
     * Display seller shop details (curent shop)
     * phpcs:disable PHPCS_SecurityAudit.BadFunctions
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionMy()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || !$user->isSeller || !$user->shop) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = $this->findModel($user->shop->id);

        try {
            $url = Yii::$app->urlManagerSDK->createAbsoluteUrl('/lib/snippet.txt');
            $snippet = htmlentities(Yii::t('app', file_get_contents($url), ['shopUri' => $user->shop->uri]));
        } catch (Throwable $ex) {
            $snippet = null;
            LogHelper::error(
                'Failed to get snippet',
                'sdk',
                LogHelper::extraForException($user->shop, $ex)
            );
        }

        return $this->render('my', [
            'model' => $model,
            'snippet' => $snippet
        ]);
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Shop();

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'The shop has been successfully created.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
                'model' => $model,
        ]);
    }

    /**
     * Updates an existing Shop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
                'model' => $model,
        ]);
    }

    /**
     * Display seller shop details (curent shop)
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateMy()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || !$user->isSeller || !$user->shop) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = $this->findModel($user->shop->id);
        $model->setScenario(Shop::SCENARIO_SELLER);
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                return $this->redirect(['my']);
            }
        }
        return $this->render('update-my', [
                'model' => $model,
        ]);
    }

    /**
     * Remove logo for specified shop
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteLogo(int $id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && (!$user->shop || $user->shop->id != $id))) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        if (!$model->deleteFile()) {
            return ['error' => 'Failed to remove logo: ' . implode(',', $model->getFirstErrors())];
        }
        $model->logo = null;
        if (!$model->save()) {
            return ['error' => 'Failed to remove logo: ' . implode(',', $model->getFirstErrors())];
        }
        return [];
    }

    /**
     * Deletes an existing Shop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'The shop `{name}` was deleted.', ['name' => $model->name]));
        }

        $referrer = Yii::$app->request->referrer;
        $viewUrl = Url::to(['view', 'id' => $id]);
        return $this->redirect($referrer && !strstr($referrer, $viewUrl) ? $referrer : ['index']);
    }

    /**
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        $model = Shop::findOne($id);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
