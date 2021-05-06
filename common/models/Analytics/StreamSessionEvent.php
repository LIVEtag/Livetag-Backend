<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Analytics;

use common\components\behaviors\TimestampBehavior;
use common\components\EventDispatcher;
use common\components\validation\validators\ArrayValidator;
use common\models\queries\Analytics\StreamSessionEventQuery;
use common\models\Stream\StreamSession;
use common\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stream_session_event".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property integer $userId
 * @property string $type
 * @property array $payload
 * @property integer $createdAt
 *
 * @property-read StreamSession $streamSession
 * @property-read User $user
 *
 * TYPES:
 * - EVENT_AFTER_INSERT
 * @see EventDispatcher
 */
class StreamSessionEvent extends ActiveRecord implements StreamSessionEventInterface
{
    /**
     * StreamSession relation key
     */
    const REL_STREAM_SESSION = 'streamSession';

    /**
     * User relation key
     */
    const REL_USER = 'user';

    /**
     * “Add to cart” clicks
     */
    const TYPE_VIEW = 'view';

    /**
     * Available type
     */
    const TYPES = [
        self::TYPE_VIEW => 'View'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session_event}}';
    }

    /**
     * @inheritdoc
     * @return StreamSessionEventQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionEventQuery
    {
        return new StreamSessionEventQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['streamSessionId', 'userId', 'type'], 'required'],
            [['streamSessionId', 'userId'], 'integer'],
            ['type', 'in', 'range' => array_keys(self::TYPES)],
            ['payload', ArrayValidator::class],
            [['streamSessionId'], 'exist', 'skipOnError' => true, 'targetClass' => StreamSession::class, 'targetRelation' => 'streamSession'],
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
            'streamSessionId' => Yii::t('app', 'Stream Session ID'),
            'userId' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'payload' => Yii::t('app', 'Payload'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id' => function () {
                return $this->getId();
            },
            'userId' => function () {
                return $this->getUserId();
            },
            'type' => function () {
                return $this->getType();
            },
            'payload' => function () {
                return $this->getPayload();
            },
            'createdAt' => function () {
                return $this->getCreatedAt();
            },
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
    public function getStreamSessionId(): ?int
    {
        return $this->streamSessionId ? (int) $this->streamSessionId : null;
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
    public function getType(): ?string
    {
        return $this->type ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getPayload(): array
    {
        return $this->payload ?: [];
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt ? (int) $this->createdAt : null;
    }

    /**
     * @param StreamSession $streamSession
     * @return StreamSessionEventQuery
     */
    public static function getActiveEventsQuery(StreamSession $streamSession): StreamSessionEventQuery
    {
        $query = self::find()->byStreamSessionId($streamSession->id);
        //For active stream - calculate all events. For stopped and archived - only events before stream stop
        if (!$streamSession->isActive()) {
            $query->andWhere(['<=', 'createdAt', $streamSession->getStoppedAt()]);
        }
        return $query;
    }

    /**
     * @param StreamSession $streamSession
     * @return StreamSessionEventQuery
     */
    public static function getArchivedEventsQuery(StreamSession $streamSession): StreamSessionEventQuery
    {
        $query = self::find()->byStreamSessionId($streamSession->id);
        //If session as stopped timestamp - calclulate all events after it
        if ($streamSession->getStoppedAt()) {
            $query->andWhere(['>', 'createdAt', $streamSession->getStoppedAt()]);
        }
        return $query;
    }
}
