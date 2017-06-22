<?php
namespace backend\controllers;

use common\components\user\SearchService;
use Yii;
use backend\components\Controller;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string|\yii\web\Response
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
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
