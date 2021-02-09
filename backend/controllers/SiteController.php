<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\controllers;

use backend\components\Controller;
use backend\models\Shop\Shop;
use backend\models\Stream\StreamSession;
use backend\models\User\User;
use common\models\forms\User\LoginForm;
use common\models\forms\User\RecoveryPassword;
use common\models\forms\User\SendRecoveryEmailForm;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use rest\common\models\AccessToken;
use Throwable;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
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
                            'actions' => ['centrifugo', 'logout', 'index'],
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

    /**
     * This is debug page for centrifugo events
     */
    public function actionCentrifugo()
    {
        $model = new DynamicModel([
            'userId',
            'accessToken',
            'shopUri',
            'streamSessionId',
            'centrifugoToken',
            'centrifugoUrl',
            'signEndpoint'
        ]);
        $model->addRule(['shopUri', 'centrifugoUrl', 'signEndpoint','streamSessionId'], 'required');
        $model->addRule(['centrifugoToken', 'centrifugoUrl'], 'string');
        $model->addRule(['signEndpoint'], 'url');
        $model->addRule('shopUri', 'string');
        $model->addRule('shopUri', 'exist', ['targetClass' => Shop::class, 'targetAttribute' => 'uri']);
        $model->addRule('streamSessionId', 'exist', ['targetClass' => StreamSession::class, 'targetAttribute' => 'id']);

        if (!$model->load(Yii::$app->request->post())) {
            //some example default values (from fixtures for quick debug)
            $model->shopUri = Shop::find()->select('uri')->scalar();//select some shop
            $model->streamSessionId = StreamSession::find()->orderBy(['id' => SORT_DESC])->select('id')->scalar(); //selet most resent session
            $model->centrifugoUrl = Yii::$app->centrifugo->ws;
            $model->signEndpoint = Yii::$app->urlManagerRest->createAbsoluteUrl('v1/centrifugo/sign');
        }
        $model->validate();
        //get access token only if form validated
        if (!$model->hasErrors()) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            //get auth token from database for current user
            $accessToken = AccessToken::find()->byUserId($user->id)->valid()->one();
            if (!$accessToken) {
                $accessToken = new AccessToken();
                $accessToken->userId = $user->id;
                $accessToken->generateToken();
                $accessToken->userIp = Yii::$app->request->getUserIP();
                $accessToken->userAgent = Yii::$app->request->getUserAgent();
                if (!$accessToken->save()) {
                    $model->addError('centrifugoToken', 'Cannot create access token: ' . implode(',', $accessToken->getFirstErrors()));
                }
            }
            if ($accessToken && !$accessToken->hasErrors()) {
                $model->accessToken = $accessToken->token;
            }
            //try to get centrifugo token
            if ($model->accessToken) {
                try {
                    $client = new Client(['verify' => false]);
                    $response = $client->post(
                        $model->signEndpoint,
                        [RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $model->accessToken]]
                    );
                    $model->centrifugoToken = ArrayHelper::getValue(Json::decode($response->getBody()), 'result.token', null);
                } catch (Throwable $ex) {
                    $model->addError('centrifugoToken', $ex->getMessage());
                }
            }
        }
        return $this->render('centrifugo', ['model' => $model]);
    }
}
