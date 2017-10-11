<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\chat\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use rest\modules\chat\controllers\ActiveController;
use rest\modules\chat\models\Channel;
use yii\filters\AccessControl;
use rest\common\models\User;

/**
 * Class ChannelController
 */
class ChannelController extends ActiveController
{

    public $modelClass = Channel::class;

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
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete'],
                            'roles' => [User::ROLE_ADVANCED],
                        ],
                    ],
                ],
            ]
        );
    }
}
