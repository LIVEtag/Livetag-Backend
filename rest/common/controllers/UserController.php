<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers;

use rest\common\controllers\actions\User\CurrentAction;
use rest\common\controllers\actions\User\OptionsAction;
use rest\common\controllers\actions\User\SignupAction;
use rest\common\models\User;
use rest\common\models\views\User\SignupUser;
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
                    'except' => ['create', 'options'],
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
                            'actions' => ['current'],
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
        return [
            'create' => [
                'class' => SignupAction::class,
                'modelClass' => SignupUser::class,
            ],
            'current' => [
                'class' => CurrentAction::class,
                'modelClass' => User::class,
            ],
            'options' => [
                'class' => OptionsAction::class,
            ],
        ];
    }
}
