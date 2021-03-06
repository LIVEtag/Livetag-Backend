<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\controllers;

use backend\components\Controller;
use backend\models\User\CreateUserForm;
use backend\models\User\User;
use backend\models\User\UserSearch;
use common\models\forms\User\ChangePasswordForm;
use common\models\forms\User\UserProfileForm;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                            'actions' => ['index', 'create', 'delete'],
                            'allow' => true,
                            'roles' => [User::ROLE_ADMIN],
                        ],
                        [
                            'actions' => ['change-password', 'view', 'change-name'],
                            'allow' => true,
                            'roles' => [User::ROLE_ADMIN, User::ROLE_SELLER],
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * Allow all for admin and current for other
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $user = Yii::$app->user->identity ?? null;
        if (!$user || !($user->isAdmin || $user->id == $id)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($shopId = null)
    {
        $model = new CreateUserForm(['shopId' => $shopId]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'The seller has been successfully created.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
                'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model (current profile)
     * For now only password change allowed
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
        $user = \Yii::$app->user->identity;

        if ($model->load(Yii::$app->request->post()) && $model->changePassword($user)) {
            return $this->redirect(['view', 'id' => $user->id]);
        }

        return $this->render('change-password', [
                'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model (current profile)
     * For now only user name changing allowed
     * If update is successful, the browser will be redirected to the previous page.
     * @return mixed
     */
    public function actionChangeName()
    {
        $user = Yii::$app->user->identity;
        $model = new UserProfileForm($user);
        $model->setAttributes($user->getAttributes());

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!$model->hasErrors()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Your name has been successfully changed!'));
                return $this->refresh();
            }
        }

        return $this->render('change-name', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'The seller {email} was deleted.', ['email' => $model->email]));
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        $model = User::findOne($id);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
