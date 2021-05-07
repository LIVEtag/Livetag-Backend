<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use rest\common\controllers\actions\Vonage\ArchiveCallbackAction;
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
    const ACTION_ARCHIVE_CALLBACK = 'archive-callback';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'except' => [self::ACTION_ARCHIVE_CALLBACK],
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [self::ACTION_ARCHIVE_CALLBACK],
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
            self::ACTION_ARCHIVE_CALLBACK => ['class' => ArchiveCallbackAction::class],
        ];
    }
}
