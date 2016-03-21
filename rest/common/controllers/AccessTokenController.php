<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers;

use rest\common\controllers\actions\AccessToken\CreateAction;
use rest\common\models\AccessToken;
use rest\components\api\Controller;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class AccessTokenController
 */
class AccessTokenController extends Controller
{
    /**
     * @inheritdoc
     */
    public $modelClass = AccessToken::class;

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
        ];
    }
}
