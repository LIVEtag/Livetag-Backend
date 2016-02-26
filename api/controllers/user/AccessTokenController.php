<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace api\controllers\user;

use api\components\rest\ActiveController;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class AccessTokenController
 */
class AccessTokenController extends ActiveController
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
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['options'],
                            'roles' => ['?'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'view', 'current', 'extend'],
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
                // Create new user token for current identity
                'create' => [
                    'class' => 'gbksoft\tokens\controllers\user\CreateAction',
                ],
                // View by user token primaryKey
                'view' => [
                    'class' => 'gbksoft\tokens\controllers\user\ViewAction',
                ],
                // Get current user token
                'current' => [
                    'class' => 'gbksoft\tokens\controllers\user\CurrentAction',
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                ],
                // Extend user token expired time
                'extend' => [
                    'class' => 'gbksoft\tokens\controllers\user\ExtendAction',
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                ],
            ]
        );
    }
}
