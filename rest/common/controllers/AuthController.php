<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Auth\FacebookAction;
use rest\common\controllers\actions\Auth\GoogleAction;
use rest\common\controllers\actions\Auth\LinkedinAction;
use rest\common\controllers\actions\Auth\OptionsAction;
use rest\common\controllers\actions\Auth\TwitterAction;
use rest\components\api\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge([
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => [
                    'facebook',
                    'linkedin',
                    'google',
                    'twitter',
                    'options'
                ],
            ],
            'access' => [
                'except' => [
                    'facebook',
                    'linkedin',
                    'google',
                    'twitter',
                    'options'
                ],
            ],
        ], parent::behaviors());
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'facebook' => [
                'class' => FacebookAction::class,
            ],
            'linkedin' => [
                'class' => LinkedinAction::class,
            ],
            'google' => [
                'class' => GoogleAction::class,
            ],
            'twitter' => [
                'class' => TwitterAction::class
            ],
            'options' => [
                'class' => OptionsAction::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'facebook' => ['POST'],
            'linkedin' => ['POST'],
            'google' => ['POST'],
            'twitter' => ['POST'],
            'options' => ['OPTIONS'],
        ];
    }
}
