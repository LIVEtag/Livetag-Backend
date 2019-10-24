<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Auth\AuthAction;
use rest\common\controllers\actions\Auth\OptionsAction;
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
                    'auth',
                    'options'
                ],
            ],
            'access' => [
                'except' => [
                    'auth',
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
            'auth' => [
                'class' => AuthAction::class,
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
            'auth' => ['POST'],
            'options' => ['OPTIONS'],
        ];
    }
}
