<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Pages\ListAction;
use rest\common\controllers\actions\Pages\ViewAction;
use rest\components\api\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class PagesController
 */
class PagesController extends Controller
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
                    'except' => ['index', 'view'],
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
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
            'index' => [
                'class' => ListAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET'],
            'view' => ['GET'],
        ];
    }
}
