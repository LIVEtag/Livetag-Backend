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
use rest\common\models\User;
use yii\web\ForbiddenHttpException;
use rest\modules\chat\models\ChannelSearch;
use rest\modules\chat\controllers\actions\JoinAction;
use rest\modules\chat\controllers\actions\LeaveAction;
use rest\modules\chat\controllers\actions\MessageAction;
use rest\modules\chat\controllers\actions\AddAction;
use rest\modules\chat\controllers\actions\RemoveAction;
use rest\modules\chat\controllers\actions\AuthAction;
use rest\modules\chat\controllers\actions\SignAction;

/**
 * Class ChannelController
 */
class ChannelController extends ActiveController
{

    /**
     * join to selected channel
     */
    const ACTION_JOIN = 'join';

    /**
     * leave selected channel
     */
    const ACTION_LEAVE = 'leave';

    /**
     * leave selected channel
     */
    const ACTION_MESSAGE = 'message';

    /**
     * add selected user to selected channel
     */
    const ACTION_ADD_TO_CHAT = 'add';

    /**
     * remove selected user from selected channel
     */
    const ACTION_REMOVE_FROM_CHAT = 'remove';

    /**
     * spetial authorisation method for private chat access check(centrifugo)
     */
    const ACTION_AUTH = 'auth';

    /**
     * get centrifugo sign key
     */
    const ACTION_SIGN = 'sign';

    /**
     * current controller default model class
     *
     * @var string
     */
    public $modelClass = Channel::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_INDEX,
                                self::ACTION_VIEW,
                                self::ACTION_JOIN,
                                self::ACTION_LEAVE,
                                self::ACTION_MESSAGE,
                                self::ACTION_AUTH,
                                self::ACTION_SIGN,
                            ],
                            'roles' => [User::ROLE_ADVANCED, User::ROLE_BASIC],
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_CREATE,
                                self::ACTION_UPDATE,
                                self::ACTION_DELETE,
                                self::ACTION_ADD_TO_CHAT,
                                self::ACTION_REMOVE_FROM_CHAT
                            ],
                            'roles' => [User::ROLE_ADVANCED],
                        ],
                    ],
                ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
                self::ACTION_INDEX => [
                    'prepareDataProvider' => [$this, 'prepareDataProvider']
                ],
                self::ACTION_AUTH => [
                    'class' => AuthAction::class,
                ],
                self::ACTION_SIGN => [
                    'class' => SignAction::class,
                ],
                self::ACTION_JOIN => [
                    'class' => JoinAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                ],
                self::ACTION_LEAVE => [
                    'class' => LeaveAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                ],
                self::ACTION_MESSAGE => [
                    'class' => MessageAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                ],
                self::ACTION_ADD_TO_CHAT => [
                    'class' => AddAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                ],
                self::ACTION_REMOVE_FROM_CHAT => [
                    'class' => RemoveAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                ],
        ]);
    }

    /**
     * Checks the privilege of the current user.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $user = Yii::$app->user->identity;
        if ($model) {
            switch ($action) {
                case self::ACTION_UPDATE:
                case self::ACTION_DELETE:
                case self::ACTION_ADD_TO_CHAT:
                case self::ACTION_REMOVE_FROM_CHAT:
                    if (!$model->canManage($user)) {
                        throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action!'));
                    }
                    break;
                case self::ACTION_VIEW:
                    if (!$model->canAccess($user)) {
                        throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action!'));
                    }
                    break;
                case self::ACTION_MESSAGE:
                    if (!$model->canPost($user)) {
                        throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action!'));
                    }
                    break;
                case self::ACTION_JOIN:
                    if ($model->type == Channel::TYPE_PRIVATE) {
                        throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to join private channels.'));
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * data provider for index action
     *
     * @return \yii\data\ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        $searchModel = new ChannelSearch();
        $params = Yii::$app->request->queryParams;
        $user = Yii::$app->user->identity;
        return $searchModel->search($params, $user);
    }
}
