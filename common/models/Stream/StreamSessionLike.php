<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\models\Stream;

use common\components\behaviors\TimestampBehavior;
use common\components\centrifugo\channels\SessionChannel;
use common\components\centrifugo\Message;
use common\helpers\LogHelper;
use common\models\queries\Stream\StreamSessionLikeQuery;
use common\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%stream_session_like}}".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property integer $userId
 * @property integer $createdAt
 *
 * @property-read StreamSession $streamSession
 * @property-read User $user
 */
class StreamSessionLike extends ActiveRecord
{
    /** @see getStreamSession() */
    const REL_STREAM_SESSION = 'streamSession';

    /** @see getUser() */
    const REL_USER = 'user';

    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'streamSessionLike';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session_like}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['streamSessionId', 'userId'], 'required'],
            [['streamSessionId', 'userId'], 'integer'],
            [
                ['streamSessionId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => StreamSession::class,
                'targetRelation' => self::REL_STREAM_SESSION,
            ],
            [
                ['userId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetRelation' => self::REL_USER,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return StreamSessionLikeQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionLikeQuery
    {
        return new StreamSessionLikeQuery(get_called_class());
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
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'streamSessionId' => Yii::t('app', 'Stream Session ID'),
            'userId' => Yii::t('app', 'User ID'),
            'createdAt' => Yii::t('app', 'Created At'),
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
            'createdAt',
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
     * Send notification about like to centrifugo
     * @param string $actionType
     */
    public function notify(string $actionType)
    {
        $streamSession = $this->streamSession;
        if ($streamSession && ($streamSession->isActive() || $streamSession->isArchived())) {
            $channel = new SessionChannel($this->streamSessionId);
            $message = new Message($actionType, $this->toArray(['createdAt']));
            if (!Yii::$app->centrifugo->publish($channel, $message)) {
                LogHelper::error('Event Failed', self::LOG_CATEGORY, [
                    'channel' => $channel->getName(),
                    'message' => $message->getBody(),
                    'actionType' => $actionType,
                    'streamSessionLike' => Json::encode($this->toArray(), JSON_PRETTY_PRINT),
                ]);
            }
        }
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
}
