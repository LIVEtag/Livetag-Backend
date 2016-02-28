<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers;

use rest\common\controllers\actions\User\SignupAction;
use rest\common\models\User;
use rest\common\models\User\Signup;
use rest\components\api\Controller;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public $modelClass = User::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'except' => ['create'],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['?'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['index', 'view', 'update', 'delete', 'current'],
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'create' => [
                    'class' => SignupAction::class,
                    'modelClass' => Signup::class,
                ],
            ]
        );
    }
}
