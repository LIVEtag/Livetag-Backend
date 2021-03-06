<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\controllers;

use common\models\Stream\StreamSession;
use rest\common\controllers\actions\Stream\Archive\ProductsAction as ArchiveProductsAction;
use rest\common\controllers\actions\Stream\Archive\SnapshotsAction;
use rest\common\controllers\actions\Stream\Archive\StartAction as ArchiveStartAction;
use rest\common\controllers\actions\Stream\Archive\StopAction as ArchiveStopAction;
use rest\common\controllers\actions\Stream\CommentCreateAction;
use rest\common\controllers\actions\Stream\CommentIndexAction;
use rest\common\controllers\actions\Stream\CreateAction;
use rest\common\controllers\actions\Stream\CurrentAction;
use rest\common\controllers\actions\Stream\EventAction;
use rest\common\controllers\actions\Stream\IndexAction;
use rest\common\controllers\actions\Stream\ProductEventAction;
use rest\common\controllers\actions\Stream\LikeCreateAction;
use rest\common\controllers\actions\Stream\LikesAction;
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
use yii\web\NotFoundHttpException;

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
    const ACTION_PRODUCTS = 'products';

    /**
     * Get snapshots of selected session
     */
    const ACTION_ARCHIVE_SNAPSHOTS = 'archive-snapshots';

    /**
     * Get presented products of selected session
     */
    const ACTION_ARCHIVE_PRODUCTS = 'archive-products';

    /**
     * Get comments list of session
     */
    const ACTION_COMMENT_INDEX = 'comment-index';

    /**
     * Get comments list of session
     */
    const ACTION_COMMENT_CREATE = 'comment-create';

    /**
     * Get likes of selected session
     */
    const ACTION_LIKES = 'likes';

    /**
     * Create like to session
     */
    const ACTION_LIKE_CREATE = 'like-create';

    /**
     *  Register session event (view)
     */
    const ACTION_EVENT = 'event';

    /**
     * Register product event (Add to cart click)
     */
    const ACTION_PRODUCT_EVENT = 'product-event';

    /**
     * Start archiving
     */
    const ACTION_ARCHIVE_START = 'archive-start';

    /**
     * Stop archiving
     */
    const ACTION_ARCHIVE_STOP = 'archive-stop';

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
                                self::ACTION_ARCHIVE_START,
                                self::ACTION_ARCHIVE_STOP,
                            ],
                            'roles' => [User::ROLE_SELLER]
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_INDEX,
                                self::ACTION_VIEW,
                                self::ACTION_CURRENT,
                                self::ACTION_TOKEN,
                                self::ACTION_PRODUCTS,
                                self::ACTION_ARCHIVE_SNAPSHOTS,
                                self::ACTION_ARCHIVE_PRODUCTS,
                                self::ACTION_COMMENT_INDEX,
                                self::ACTION_COMMENT_CREATE,
                                self::ACTION_LIKE_CREATE,
                                self::ACTION_LIKES,
                            ],
                            'roles' => [User::ROLE_SELLER, User::ROLE_BUYER]
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                self::ACTION_EVENT,
                                self::ACTION_PRODUCT_EVENT,
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
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_CURRENT => CurrentAction::class,
                self::ACTION_INDEX => IndexAction::class,
                self::ACTION_VIEW => [
                    'class' => ViewAction::class,
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_STOP => [
                    'class' => StopAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_TOKEN => [
                    'class' => TokenAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_PRODUCTS => [
                    'class' => ProductsAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_ARCHIVE_SNAPSHOTS => [
                    'class' => SnapshotsAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_ARCHIVE_PRODUCTS => [
                    'class' => ArchiveProductsAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_COMMENT_INDEX => [
                    'class' => CommentIndexAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_COMMENT_CREATE => [
                    'class' => CommentCreateAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_LIKE_CREATE => [
                    'class' => LikeCreateAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_LIKES => [
                    'class' => LikesAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_EVENT => [
                    'class' => EventAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_PRODUCT_EVENT => [
                    'class' => ProductEventAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'findModel' => [$this, 'findModel'],
                ],
                self::ACTION_ARCHIVE_START => [
                    'class' => ArchiveStartAction::class,
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
                self::ACTION_ARCHIVE_STOP => [
                    'class' => ArchiveStopAction::class,
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
            case self::ACTION_ARCHIVE_START:
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
                $model->checkCanAddComment($user);
                break;
            case self::ACTION_COMMENT_INDEX:
                if (!$model || !$user) {
                    throw new ForbiddenHttpException('You are not allowed to access this entity.'); //just in case
                }
                if (!$model->getCommentsEnabled()) {
                    throw new ForbiddenHttpException('Comment section of the widget was disabled');
                }
                break;
            default:
                break;
        }
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = StreamSession::find()
            ->byId($id)
            ->published()
            ->one();

        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException("Stream Session not found by id: $id");
    }
}
