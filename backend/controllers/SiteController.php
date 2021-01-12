<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\User\User;
use common\models\forms\User\LoginForm;
use common\models\forms\User\RecoveryPassword;
use common\models\forms\User\SendRecoveryEmailForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
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
                            'actions' => ['login', 'error', 'forgot-password', 'reset-password'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['logout', 'index'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'actions' => [
                        'logout' => ['post'],
                    ],
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * @return string|Response
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $loginForm = \Yii::createObject(LoginForm::class);
        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $loginForm,
        ]);
    }
    
    /**
     * @return mixed
     */
    public function actionForgotPassword()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = '//main-login';
        
        $model = new SendRecoveryEmailForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->generateAndSendEmail();
            Yii::$app->session->setFlash(
                'success',
                "We have sent a confirmation email to your email address: {$model->email}.
                Please follow the instructions in the email to continue."
            );
            return $this->redirect('login');
        }

        return $this->render('forgot-password', [
            'model' => $model,
        ]);
    }
    
    /**
     * @param string $token
     * @return mixed
     */
    public function actionResetPassword(string $token)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = '//main-login';
        
        $user = User::findByPasswordResetToken($token);
        if ($user === null) {
            return $this->render('error', ['name' => '404', 'message' => 'Token is invalid.']);
        }
        
        /** @var RecoveryPassword $model */
        $model = \Yii::createObject(RecoveryPassword::class);
        $model->resetToken = $token;
        if (!$model->hasErrors() && $model->load(Yii::$app->request->post())) {
            $user = User::findByPasswordResetToken($model->resetToken);
            $model->recovery($user);
            return $this->redirect('login');
        }
        
        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }
    
    /**
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
