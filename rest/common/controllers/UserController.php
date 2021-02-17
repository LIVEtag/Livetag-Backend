<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\User\ChangePasswordAction;
use rest\common\controllers\actions\User\CurrentAction;
use rest\common\controllers\actions\User\LogoutAction;
use rest\common\controllers\actions\User\NewPasswordAction;
use rest\common\controllers\actions\User\OptionsAction;
use rest\common\controllers\actions\User\RecoveryAction;
use rest\common\controllers\actions\User\ValidatePasswordTokenAction;
use rest\common\models\User;
use rest\components\api\Controller;
use rest\components\filters\RateLimiter\Rules\RouteRateLimitRule;
use yii\helpers\ArrayHelper;

/**
 * Class UserController
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
                'authenticator' => [
                    'except' => ['options', 'recovery-password', 'new-password', 'validate-password-token'],
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['recovery-password', 'new-password', 'validate-password-token'],
                            'roles' => ['?'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['current'],
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['logout', 'change-password', 'validate-password-token'],
                            'roles' => [User::ROLE_SELLER, User::ROLE_ADMIN], //this actions NOT supported for buyer
                        ],
                    ],
                ],
                'rateLimiter' => [
                    'rules' => [
                        [
                            'class' => RouteRateLimitRule::class,
                            'actions' => [
                                'recovery-password',
                            ],
                            'maxCount' => 3,
                            'interval' => 60,
                        ],
                    ],
                    'isActive' => YII_ENV_PROD
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
            'current' => [
                'class' => CurrentAction::class,
            ],
            'options' => [
                'class' => OptionsAction::class,
            ],
            'change-password' => [
                'class' => ChangePasswordAction::class
            ],
            'recovery-password' => [
                'class' => RecoveryAction::class
            ],
            'new-password' => [
                'class' => NewPasswordAction::class
            ],
            'validate-password-token' => [
                'class' => ValidatePasswordTokenAction::class
            ],
            'logout' => [
                'class' => LogoutAction::class
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'current' => ['GET'],
            'options' => ['OPTIONS'],
            'changePassword' => ['PATCH'],
            'recovery-password' => ['POST'],
            'new-password' => ['POST'],
            'validate-password-token' => ['GET'],
            'logout' => ['POST'],
        ];
    }
}
