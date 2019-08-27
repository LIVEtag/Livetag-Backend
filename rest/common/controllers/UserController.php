<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\User\OptionsAction;
use rest\common\controllers\actions\User\RecoveryAction;
use rest\common\controllers\actions\User\SignupAction;
use rest\common\controllers\actions\User\ChangePasswordAction;
use rest\common\controllers\actions\User\CurrentAction;
use rest\common\controllers\actions\User\NewPasswordAction;
use rest\common\controllers\actions\User\LogoutAction;
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
                    'except' => ['create', 'options', 'recovery-password', 'new-password'],
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['create', 'recovery-password', 'new-password'],
                            'roles' => ['?'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['current', 'change-password', 'logout'],
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'rateLimiter' => [
                    'rules' => [
                        [
                            'class' => RouteRateLimitRule::class,
                            'actions' => [
                                'recovery-password',
                                'new-password',
                            ],
                            'maxCount' => 3,
                            'interval' => 3600,
                        ],
                    ]
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
            ],
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
            'logout' => ['POST'],
        ];
    }
}
