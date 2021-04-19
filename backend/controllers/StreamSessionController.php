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
use backend\models\Product\Product;
use backend\models\Product\StreamSessionProduct;
use backend\models\Product\StreamSessionProductSearch;
use backend\models\Stream\SaveAnnouncementForm;
use backend\models\Stream\StreamSession;
use backend\models\Stream\StreamSessionSearch;
use backend\models\Stream\UploadRecordedShowForm;
use backend\models\User\User;
use common\models\forms\Comment\CommentForm;
use Exception;
use kartik\grid\EditableColumnAction;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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
                            'actions' => [
                                'create',
                                'update',
                                'publish',
                                'unpublish',
                                'delete-cover-image',
                                'upload-recorded-show',
                            ],
                            'allow' => true,
                            'roles' => [User::ROLE_SELLER],
                        ],
                        [
                            'actions' => [
                                'index',
                                'view',
                                self::ACTION_EDITABLE_PRODUCT,
                                'create-comment',
                                'delete-comment',
                                'delete-product',
                                'enable-comment',
                                'stop'
                            ],
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
                        'delete-cover-image' => ['POST'],
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
        $user = $this->getAndCheckCurrentUser();

        $searchModel = new StreamSessionSearch();
        $params = Yii::$app->request->queryParams;
        if ($user->isSeller) {
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($searchModel)) => ['shopId' => $user->shop->id]]);
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new StreamSession model. (only for seller)
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var User $user */
        $user = $this->getAndCheckCurrentUser();

        $model = new SaveAnnouncementForm();
        $params = Yii::$app->request->post();
        if ($params) {
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($model)) => ['shopId' => $user->shop->id]]);
        }
        if ($model->load($params)) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->streamSession->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'productIds' => Product::getIndexedArray($user->shop->id)
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionUploadRecordedShow()
    {
        /** @var User $user */
        $user = $this->getAndCheckCurrentUser();

        $model = new UploadRecordedShowForm();
        $params = Yii::$app->request->post();
        if ($params) { //shop and seller checked before
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($model)) => ['shopId' => $user->shop->id]]);
        }
        if ($model->load($params)) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->streamSession->id]);
            }
        }

        return $this->render('upload-recorded-show', [
            'model' => $model,
            'productIds' => Product::getIndexedArray($user->shop->id),
        ]);
    }

    /**
     * Updates an existing StreamSession model. (only for seller)
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $streamSession = $this->findModel($id);
        $user = Yii::$app->user->identity;//shop and seller checked before

        $model = new SaveAnnouncementForm($streamSession);
        $params = Yii::$app->request->post();
        if ($params) { //shop and seller checked before
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($model)) => ['shopId' => $user->shop->id]]);
        }
        if ($model->load($params)) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->streamSession->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'productIds' => Product::getIndexedArray($user->shop->id)
        ]);
    }


    /**
     * Displays a single StreamSession model.
     * phpcs:disable PHPCS_SecurityAudit.BadFunctions
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        /** @var User $user */
        $user = $this->getAndCheckCurrentUser();

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

        try {
            $url = Yii::$app->urlManagerSDK->createAbsoluteUrl('/lib/watch-session.txt');
            $snippet = htmlentities(Yii::t('app', file_get_contents($url), ['sessionId' => $id])); //todo: cache it
        } catch (Throwable $ex) {
            $snippet = null;
            LogHelper::error(
                'Failed to get snippet',
                'sdk',
                LogHelper::extraForException($user->shop, $ex)
            );
        }

        $method = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return $this->$method('view', [
                'model' => $model,
                'productSearchModel' => $productSearchModel,
                'productDataProvider' => $productDataProvider,
                'commentSearchModel' => $commentSearchModel,
                'commentDataProvider' => $commentDataProvider,
                'commentModel' => $commentModel,
                'snippet' => $snippet
        ]);
    }

    /**
     * Delete cover image from stream session
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteCoverImage(int $id)
    {
        $model = $this->findModel($id);
        $cover = $model->streamSessionCover;

        if (!$cover) {
            Yii::$app->session->setFlash('error', 'The stream session doesn\'t have cover image.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($cover->delete() === false) {
            Yii::$app->session->setFlash('error', 'Failed to remove cover image.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        Yii::$app->session->setFlash('success', Yii::t('app', 'The cover image was removed.'));
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Create Comment
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function actionCreateComment(int $id)
    {
        /** @var User $user */
        $user = $this->getAndCheckCurrentUser();
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
        } catch (ForbiddenHttpException $ex) {
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
     * Publish an existing Stream
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionPublish(int $id)
    {
        try {
            if (!$this->findModel($id)->publish()) {
                throw new Exception('Live stream publication status was not updated.');
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Live stream was published.'));
        } catch (Throwable $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Unpublish an existing Stream
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionUnpublish(int $id)
    {
        try {
            if (!$this->findModel($id)->unpublish()) {
                throw new Exception('Live stream publication status was not updated.');
            }
            Yii::$app->session->setFlash(
                'success',
                Yii::t('app', 'Live stream was unpublished.')
            );
        } catch (Throwable $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Deletes a Product from Session.
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
        $user = $this->getAndCheckCurrentUser();
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
        $user = $this->getAndCheckCurrentUser();
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
        $user = $this->getAndCheckCurrentUser();
        $model = StreamSession::findOne($id);
        if (!$model || ($user->isSeller && $user->shop->id != $model->shopId)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }

    /**
     * Get user. Check shop exist for seller
     * @return User
     * @throws NotFoundHttpException
     */
    protected function getAndCheckCurrentUser()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $user;
    }
}
