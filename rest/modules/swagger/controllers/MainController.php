<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\swagger\controllers;

use rest\modules\swagger\controllers\actions\Main\HistoryAction;
use rest\modules\swagger\controllers\actions\Main\JsonAction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Class MainController
 */
class MainController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $isSecure = Yii::$app->getRequest()->getIsSecureConnection();

        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['json', 'history'],
                            'allow' => true,
                        ],
                    ],
                ],
                'corsFilter' => [
                    'class' => Cors::className(),
                    'cors' => [
                        'Origin' => [
                            $isSecure ? 'https://' : 'http://'. Yii::getAlias('@backend.domain'),
                        ],
                        'Access-Control-Request-Method' => ['GET'],
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
            'history' => [
                'class' => HistoryAction::class,
            ],
            'json' => [
                'class' => JsonAction::class
            ]
        ];
    }
}
