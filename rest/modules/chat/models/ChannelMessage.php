<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace rest\modules\chat\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property integer $channelId
 * @property integer $userId
 * @property string $message
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property Channel $channel
 * @property User $user
 */
class ChannelMessage extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'channel_message';
    }

    /**
     * @inheritdoc
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
            [['channelId', 'userId', 'message'], 'required'],
            [['channelId', 'userId'], 'integer'],
            [['message'], 'string', 'max' => 255],
            [
                ['channelId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Channel::class,
                'targetAttribute' => ['channelId' => 'id']
            ],
            [
                ['userId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['userId' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'user',
            'message',
            'date' => function () {
                return $this->createdAt;
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            'user',
            'channel'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'channelId' => Yii::t('app', 'Channel ID'),
            'userId' => Yii::t('app', 'User ID'),
            'message' => Yii::t('app', 'Message'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChannel(): ?ActiveQuery
    {
        return $this->hasOne(Channel::className(), ['id' => 'channelId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ?ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @inheritdoc
     *
     * before save message->publish it to centrifugo channel
     */
    public function beforeSave($insert): bool
    {
        if (!Yii::$app->getModule('chat')
            ->centrifugo->setUser($this->user)
            ->publishMessage($this->channel->url, $this->message)
        ) {
            $this->addError('channelId', Yii::t('app', 'Failed to send message to channel'));
            return false;
        }

        return parent::beforeSave($insert);
    }
}
