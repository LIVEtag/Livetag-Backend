<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Vonage\WebhookAction;
use rest\components\api\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class VonageController
 */
class VonageController extends Controller
{
    /**
     * Monitor vonage(tokbox) status and save it when it created
     * @see https://tokbox.com/developer/guides/archiving/#status
     */
    const ACTION_WEBHOOK = 'webhook';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'except' => [self::ACTION_WEBHOOK],
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [self::ACTION_WEBHOOK],
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
            self::ACTION_WEBHOOK => ['class' => WebhookAction::class],
        ];
    }
}
