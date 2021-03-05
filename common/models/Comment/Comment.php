<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Comment;

use common\components\behaviors\TimestampBehavior;
use common\components\centrifugo\channels\SessionChannel;
use common\components\centrifugo\Message;
use common\components\EventDispatcher;
use common\helpers\LogHelper;
use common\models\queries\Comment\CommentQuery;
use common\models\queries\Stream\StreamSessionQuery;
use common\models\Stream\StreamSession;
use common\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $streamSessionId
 * @property string $message
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read StreamSession $streamSession
 * @property-read User $user
 *
 * EVENTS:
 * - EVENT_AFTER_INSERT
 * - EVENT_AFTER_UPDATE
 * - EVENT_AFTER_DELETE
 * @see EventDispatcher
 */
class Comment extends ActiveRecord implements CommentInterface
{
    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'comment';

    /**
     * User relation key
     */
    const REL_USER = 'user';

    /**
     * StreamSession relation key
     */
    const REL_STREAM_SESSION = 'streamSession';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find(): CommentQuery
    {
        return new CommentQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['userId', 'streamSessionId', 'message'], 'required'],
            [['userId', 'streamSessionId'], 'integer'],
            ['message', 'string'],
            [
                'streamSessionId',
                'exist',
                'skipOnError' => true,
                'targetClass' => StreamSession::class,
                'targetRelation' => 'streamSession',
                'filter' => function (StreamSessionQuery $query) {
                    return $query->active()->commentsEnabled();
                }
            ],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetRelation' => 'user'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'streamSessionId' => Yii::t('app', 'Stream Session ID'),
            'message' => Yii::t('app', 'Message'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'userId',
            'message',
            'createdAt'
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            self::REL_USER,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        return $this->id ? (int) $this->id : null;
    }

    /**
     * @inheritdoc
     */
    public function getUserId(): ?int
    {
        return $this->userId ? (int) $this->userId : null;
    }

    /**
     * @inheritdoc
     */
    public function getStreamSessionId(): ?int
    {
        return $this->streamSessionId ? (int) $this->streamSessionId : null;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt ? (int) $this->createdAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt ? (int) $this->updatedAt : null;
    }

    /**
     * Send notification about comments to centrifugo
     * @param string $actionType
     */
    public function notify(string $actionType)
    {
        $channel = new SessionChannel($this->getStreamSessionId());
        $message = new Message($actionType, $this->toArray([], [self::REL_USER]));
        if (!Yii::$app->centrifugo->publish($channel, $message)) {
            LogHelper::error('Event Failed', self::LOG_CATEGORY, [
                'channel' => $channel->getName(),
                'message' => $message->getBody(),
                'actionType' => $actionType,
                'streamSessionProduct' => Json::encode($this->toArray(), JSON_PRETTY_PRINT),
            ]);
        }
    }
}
