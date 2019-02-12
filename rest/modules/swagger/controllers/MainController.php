<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\swagger\controllers;

use rest\modules\swagger\controllers\actions\Main\HistoryAction;
use rest\modules\swagger\controllers\actions\Main\JsonAction;
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
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['json', 'history'],
                            'allow' => true,
                        ],
                    ],
                ],
                'corsFilter' => [
                    'class' => Cors::class,
                    'cors' => [
                        'Origin' => ['*'],
                        'Access-Control-Request-Method' => ['GET'],
                        'Access-Control-Request-Headers' => ['*'],
                        'Access-Control-Max-Age' => 86400,
                        'Access-Control-Allow-Credentials' => false,
                        'Access-Control-Allow-Headers' => ['Content-Type'],
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
            'json' => [
                'class' => JsonAction::class
            ]
        ];
    }
}
