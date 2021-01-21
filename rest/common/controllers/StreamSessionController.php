<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Stream\CreateAction;
use rest\common\controllers\actions\Stream\StopAction;
use rest\common\controllers\actions\Stream\ViewAction;
use rest\common\models\User;
use rest\components\api\Controller;
use yii\helpers\ArrayHelper;

/**
 * StreamSessionController implements the CRUD actions for StreamSession model.
 */
class StreamSessionController extends Controller
{
    /**
     * Create Vonage Session
     */
    const ACTION_CREATE = 'create';

    /**
     * Stop Current active Vonage Session
     */
    const ACTION_STOP = 'stop';

    /**
     * Get Current Active Vonage Session for shop
     */
    const ACTION_VIEW = 'view';

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_CREATE,
                                self::ACTION_STOP,
                            ],
                            'roles' => [User::ROLE_SELLER]
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_VIEW,
                            ],
                            'roles' => [User::ROLE_SELLER, User::ROLE_BUYER]
                        ],
                    ],
                ]
            ]
        );
        return $behaviors;
    }

    /**
     * @return array
     */
    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                self::ACTION_CREATE => CreateAction::class,
                self::ACTION_STOP => StopAction::class,
                self::ACTION_VIEW => ViewAction::class,
            ]
        );
    }
}
