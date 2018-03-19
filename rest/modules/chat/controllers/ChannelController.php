<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace rest\modules\chat\controllers;

use rest\modules\chat\controllers\actions\AddAction;
use rest\modules\chat\controllers\actions\AuthAction;
use rest\modules\chat\controllers\actions\GetMessagesAction;
use rest\modules\chat\controllers\actions\GetUsersAction;
use rest\modules\chat\controllers\actions\JoinAction;
use rest\modules\chat\controllers\actions\LeaveAction;
use rest\modules\chat\controllers\actions\MessageAction;
use rest\modules\chat\controllers\actions\RemoveAction;
use rest\modules\chat\controllers\actions\SignAction;
use rest\modules\chat\models\Channel;
use rest\modules\chat\models\ChannelSearch;
use rest\modules\chat\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

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
     * get users in channel
     */
    const ACTION_GET_USERS = 'get_users';

    /**
     * get messages in channel
     */
    const ACTION_GET_MESSAGES = 'get_messages';

    /**
     * add message to channel
     */
    const ACTION_ADD_MESSAGE = 'add_message';

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
     * @var string the scenario used for updating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $updateScenario = Channel::SCENARIO_UPDATE;

    /**
     * @var string the scenario used for creating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $createScenario = Channel::SCENARIO_CREATE;

    /**
     * current controller default model class
     *
     * @var string
     */
    public $modelClass = Channel::class;

    /**
     * @inheritdoc
     */
    public function behaviors(): array
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
                            self::ACTION_ADD_MESSAGE,
                            self::ACTION_GET_MESSAGES,
                            self::ACTION_GET_USERS,
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
    public function actions(): array
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
            self::ACTION_GET_MESSAGES => [
                'class' => GetMessagesAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            self::ACTION_GET_USERS => [
                'class' => GetUsersAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            self::ACTION_ADD_MESSAGE => [
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
        $userId = Yii::$app->getModule('chat')->user->id;
        if ($model) {
            switch ($action) {
                case self::ACTION_UPDATE:
                case self::ACTION_DELETE:
                case self::ACTION_ADD_TO_CHAT:
                case self::ACTION_REMOVE_FROM_CHAT:
                    if (!$model->canManage($userId)) {
                        throw new ForbiddenHttpException(
                            Yii::t('app', 'You do no have permissions to manage this channel')
                        );
                    }
                    break;
                case self::ACTION_VIEW:
                case self::ACTION_GET_MESSAGES:
                case self::ACTION_GET_USERS:
                    if (!$model->canAccess($userId)) {
                        throw new ForbiddenHttpException(
                            Yii::t('app', 'You are not allowed to perform this action!')
                        );
                    }
                    break;
                case self::ACTION_ADD_MESSAGE:
                    if (!$model->canPost($userId)) {
                        throw new ForbiddenHttpException(
                            Yii::t('app', 'You do no have permissions to post message in this channel')
                        );
                    }
                    break;
                case self::ACTION_JOIN:
                    if ($model->type == Channel::TYPE_PRIVATE) {
                        throw new ForbiddenHttpException(
                            Yii::t('app', 'You are not allowed to join private channels.')
                        );
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
     * @return ActiveDataProvider
     */
    public function prepareDataProvider(): ActiveDataProvider
    {
        $searchModel = new ChannelSearch();
        $params = Yii::$app->request->queryParams;
        $user = Yii::$app->getModule('chat')->user->identity;
        return $searchModel->search($params, $user);
    }
}
