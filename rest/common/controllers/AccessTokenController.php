<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\AccessToken\CreateAction;
use rest\common\controllers\actions\AccessToken\EmailAction;
use rest\common\controllers\actions\AccessToken\OptionsAction;
use rest\components\api\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class AccessTokenController
 */
class AccessTokenController extends Controller
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
                    'except' => ['create', 'options', 'email'],
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['create', 'email'],
                            'roles' => ['?'],
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
                'class' => CreateAction::class,
            ],
            'options' => [
                'class' => OptionsAction::class,
            ],
            'email' => [
                'class' => EmailAction::class
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
            'email' => ['POST'],
        ];
    }
}
