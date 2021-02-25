<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\controllers;

use backend\components\Controller;
use backend\models\Comment\Comment;
use backend\models\Comment\CommentSearch;
use backend\models\Product\StreamSessionProduct;
use backend\models\Product\StreamSessionProductSearch;
use backend\models\Stream\StreamSession;
use backend\models\Stream\StreamSessionSearch;
use backend\models\User\User;
use common\models\forms\Comment\CommentForm;
use kartik\grid\EditableColumnAction;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * StreamSessionController implements the CRUD actions for StreamSession model.
 */
class StreamSessionController extends Controller
{
    /**
     * Editable Action
     */
    const ACTION_EDITABLE_PRODUCT = 'editable-product';

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
                            'roles' => [User::ROLE_ADMIN, User::ROLE_SELLER], //todo: add RBAC with check access
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'stop' => ['POST'],
                        'delete' => ['POST'],
                    ],
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function actions()
    {
        return [
            self::ACTION_EDITABLE_PRODUCT => [
                'class' => EditableColumnAction::class,
                'modelClass' => StreamSessionProduct::class
            ],
        ];
    }

    /**
     * Lists all StreamSession models.
     * Display only sellers sessions (by shopId) for seller role
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $searchModel = new StreamSessionSearch();
        $params = Yii::$app->request->queryParams;
        if ($user->isSeller) {
            $params = ArrayHelper::merge($params, [StringHelper::basename(\get_class($searchModel)) => ['shopId' => $user->shop->id]]);
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StreamSession model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $model = $this->findModel($id);

        $productSearchModel = new StreamSessionProductSearch();
        //modify params for search models and pagination
        $params = ArrayHelper::merge(
            Yii::$app->request->queryParams,
            [StringHelper::basename(\get_class($productSearchModel)) => ['streamSessionId' => $model->id]]
        );
        $productDataProvider = $productSearchModel->search($params);

        $commentSearchModel = new CommentSearch();
        //modify params for search models and pagination
        $paramsComment = ArrayHelper::merge(
            Yii::$app->request->queryParams,
            [StringHelper::basename(\get_class($commentSearchModel)) => ['streamSessionId' => $model->id]]
        );
        $commentDataProvider = $commentSearchModel->search($paramsComment);

        $commentModel = new CommentForm();
        $commentModel->streamSessionId = $id;
        $commentModel->userId = $user->id;
        if ($commentModel->load(Yii::$app->request->post()) && $commentModel->save()) {
            $commentModel = new CommentForm();
            $commentModel->streamSessionId = $id;
            $commentModel->userId = $user->id;
        }

        $method = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return $this->$method('view', [
                'model' => $model,
                'productSearchModel' => $productSearchModel,
                'productDataProvider' => $productDataProvider,
                'commentSearchModel' => $commentSearchModel,
                'commentDataProvider' => $commentDataProvider,
                'commentModel' => $commentModel,
        ]);
    }

    /**
     * Create Comment
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function actionCreateComment(int $id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        /** @var StreamSession $model */
        $model = $this->findModel($id); //get entity and check access
        $commentModel = new CommentForm(['streamSessionId' => $id]);
        try {
            $model->checkCanAddComment($user);
            if ($commentModel->load(Yii::$app->request->post())) {
                $commentModel->userId = $user->id;
                if ($commentModel->save()) {
                    $commentModel = new CommentForm(['streamSessionId' => $id]); //reset form
                }
            }
        } catch (\yii\web\ForbiddenHttpException $ex) {
            $commentModel->addError('message', $ex->getMessage());
        }
        $method = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return $this->$method('comment-form', [
                'commentModel' => $commentModel,
                'streamSessionId' => $model->id,
        ]);
    }

    /**
     * Enable/Disable comments in Stream Session
     * @param int $id
     */
    public function actionEnableComment(int $id)
    {
        /** @var StreamSession $model */
        $model = $this->findModel($id); //get entity and check access

        //quite fast solution without form
        $model->commentsEnabled = (bool) Yii::$app->request->post('commentsEnabled');
        $model->save(true, ['commentsEnabled']);
        $method = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return $this->$method('comment-enable-form', [
            'streamSession' => $model,
        ]);
    }

    /**
     * Stop an existing Stream
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionStop(int $id)
    {
        try {
            $this->findModel($id)->stop();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Live stream is over.'));
        } catch (Throwable $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Deletes aProduct from Session.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteProduct(int $id)
    {
        $model = $this->findProductModel($id);
        $model->delete();
        if (Yii::$app->request->isAjax) {
            return $this->productList($model->streamSessionId);
        }
        $streamUrl = Url::to(['view', 'id' => $model->streamSessionId]);
        return $this->redirect($streamUrl);
    }

    /**
     * Deletes comment from Session.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteComment(int $id)
    {
        $model = $this->findCommentModel($id);
        $model->delete();
        if (Yii::$app->request->isAjax) {
            return $this->commentList($model->streamSessionId);
        }
        $streamUrl = Url::to(['view', 'id' => $model->streamSessionId]);
        return $this->redirect(Yii::$app->request->referrer ?: [$streamUrl]);
    }

    /**
     * @param int $streamSessionId
     */
    protected function productList(int $streamSessionId)
    {
        $productSearchModel = new StreamSessionProductSearch();
        //modify params for search models and pagination
        $queryParams = Yii::$app->request->getQueryParams();
        //extract original filters from referrer
        $parts = parse_url(Yii::$app->request->referrer);
        $referrerParams = [];
        mb_parse_str($parts['query'], $referrerParams);
        //merge all params
        $params = ArrayHelper::merge(
            $queryParams,
            $referrerParams,
            [StringHelper::basename(get_class($productSearchModel)) => ['streamSessionId' => $streamSessionId]]
        );
        //set params back to use sorting
        Yii::$app->request->setQueryParams($params);

        $productDataProvider = $productSearchModel->search($params);
        $productDataProvider->sort->route = '/stream-session/view';
        $productDataProvider->pagination->route = '/stream-session/view';
        $method = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return $this->$method('product-index', [
                'productSearchModel' => $productSearchModel,
                'productDataProvider' => $productDataProvider,
                'streamSessionId' => $streamSessionId,
        ]);
    }

    /**
     * @param int $streamSessionId
     */
    protected function commentList(int $streamSessionId)
    {
        $commentSearchModel = new CommentSearch();
        //modify params for search models and pagination
        $queryParams = Yii::$app->request->getQueryParams();
        //extract original filters from referrer
        $parts = parse_url(Yii::$app->request->referrer);
        $referrerParams = [];
        mb_parse_str($parts['query'], $referrerParams);
        //merge all params
        $params = ArrayHelper::merge(
            $queryParams,
            $referrerParams,
            [StringHelper::basename(\get_class($commentSearchModel)) => ['streamSessionId' => $streamSessionId]]
        );
        //set params back to use sorting
        Yii::$app->request->setQueryParams($params);

        $commentDataProvider = $commentSearchModel->search($params);
        $commentDataProvider->sort->route = '/stream-session/view';
        $commentDataProvider->pagination->route = '/stream-session/view';
        $method = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return $this->$method('comment-index', [
            'commentSearchModel' => $commentSearchModel,
            'commentDataProvider' => $commentDataProvider,
            'streamSessionId' => $streamSessionId,
        ]);
    }

    /**
     * Finds the StreamSessionProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @todo add RBAC for access check
     *
     * @param integer $id
     * @return StreamSessionProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findProductModel(int $id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = StreamSessionProduct::findOne($id);
        if (!$model || ($user->isSeller & $model->streamSession->shopId != $user->shop->id)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }

    /**
     * Finds the StreamSessionProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @todo add RBAC for access check
     *
     * @param integer $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCommentModel(int $id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = Comment::findOne($id);
        if (!$model || ($user->isSeller & $model->streamSession->shopId != $user->shop->id)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }

    /**
     * Finds the StreamSession model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StreamSession the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = StreamSession::findOne($id);
        if (!$model || ($user->isSeller && $user->shop->id != $model->shopId)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }
}
