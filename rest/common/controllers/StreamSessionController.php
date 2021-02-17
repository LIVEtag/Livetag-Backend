<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use common\models\Stream\StreamSession;
use rest\common\controllers\actions\Stream\EventAction;
use rest\common\controllers\actions\Stream\CommentCreateAction;
use rest\common\controllers\actions\Stream\CommentIndexAction;
use rest\common\controllers\actions\Stream\CreateAction;
use rest\common\controllers\actions\Stream\CurrentAction;
use rest\common\controllers\actions\Stream\ProductsAction;
use rest\common\controllers\actions\Stream\StartAction;
use rest\common\controllers\actions\Stream\StopAction;
use rest\common\controllers\actions\Stream\TokenAction;
use rest\common\models\User;
use rest\components\api\ActiveController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\ViewAction;
use yii\web\ForbiddenHttpException;

/**
 * StreamSessionController implements the CRUD actions for StreamSession model.
 */
class StreamSessionController extends ActiveController
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = StreamSession::class;

    /**
     * Create Vonage Session
     */
    const ACTION_CREATE = 'create';

    /**
     * View session details (by id)
     */
    const ACTION_VIEW = 'view';

    /**
     * Start translation (create publisher token)
     */
    const ACTION_START = 'start';

    /**
     * Stop Current active Vonage Session
     */
    const ACTION_STOP = 'stop';

    /**
     * Get Current Active Vonage Session for shop
     */
    const ACTION_CURRENT = 'current';

    /**
     * Get token for selected session
     */
    const ACTION_TOKEN = 'token';

    /**
     * Get products of selected session
     */
    const ACTION_PRODUCTS = 'produtcs';

    /**
     * Get comments list of session
     */
    const ACTION_COMMENT_INDEX = 'comment-index';

    /**
     * Get comments list of session
     */
    const ACTION_COMMENT_CREATE = 'comment-create';

    /**
     * Add to cart click event
     */
    const ACTION_EVENT = 'event';

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
                                self::ACTION_START,
                                self::ACTION_STOP,
                            ],
                            'roles' => [User::ROLE_SELLER]
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_VIEW,
                                self::ACTION_CURRENT,
                                self::ACTION_TOKEN,
                                self::ACTION_PRODUCTS,
                                self::ACTION_COMMENT_INDEX,
                                self::ACTION_COMMENT_CREATE,
                            ],
                            'roles' => [User::ROLE_SELLER, User::ROLE_BUYER]
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_EVENT,
                            ],
                            'roles' => [User::ROLE_BUYER]
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
                self::ACTION_START => [
                    'class' => StartAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
                self::ACTION_CURRENT => CurrentAction::class,
                self::ACTION_VIEW => ['class' => ViewAction::class],
                self::ACTION_STOP => [
                    'class' => StopAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
                self::ACTION_TOKEN => [
                    'class' => TokenAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
                self::ACTION_PRODUCTS => [
                    'class' => ProductsAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
                self::ACTION_COMMENT_INDEX => [
                    'class' => CommentIndexAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
                self::ACTION_COMMENT_CREATE => [
                    'class' => CommentCreateAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
                self::ACTION_EVENT => [
                    'class' => EventAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
            ]
        );
    }

    /**
     * Check if current user can manipulate with selected entity
     *
     * @param string $action the ID of the action to be executed
     * @param StreamSession $model the model to be accessed. If null, it means no specific model is being accessed.
     * @throws ForbiddenHttpException if the user does not have access
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        /** @var $user User */
        $user = Yii::$app->user->identity ?? null;
        switch ($action) {
            case self::ACTION_START:
            case self::ACTION_STOP:
                if (!$model || !$user) {
                    throw new ForbiddenHttpException('You are not allowed to access this entity.'); //just in case
                }
                if (!$user->shop || $user->shop->id !== $model->shopId) {
                    throw new ForbiddenHttpException('You are not allowed to access this entity.');
                }
                break;
            case self::ACTION_COMMENT_CREATE:
                if (!$model || !$user) {
                    throw new ForbiddenHttpException('You are not allowed to access this entity.'); //just in case
                }
                //Do not allow seller from another shop post a comment
                if ($user->isSeller && (!$user->shop || $user->shop->id !== $model->shopId)) {
                    throw new ForbiddenHttpException('You can not leave comments in non-your broadcast.');
                } elseif ($user->isBuyer && !$user->name) {
                    throw new ForbiddenHttpException('You cannot leave a comment without specifying a name.');
                }
                break;
            default:
                break;
        }
    }
}
