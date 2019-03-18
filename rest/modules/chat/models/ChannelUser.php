<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace rest\modules\chat\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "channel_user".
 *
 * @property integer $id
 * @property integer $channelId
 * @property integer $userId
 * @property integer $role
 *
 * @property Channel $channel
 * @property User $user
 */
class ChannelUser extends ActiveRecord
{

    /**
     * user do not have access in channel
     */
    const ROLE_NOBODY = 0;

    /**
     * regular user. Can access to channel and post messages
     */
    const ROLE_USER = 1;

    /**
     * channel creator. Also can manage channel
     */
    const ROLE_ADMIN = 2;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'channel_user';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['channelId', 'userId'], 'required'],
            [['channelId', 'userId', 'role'], 'integer'],
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
            [
                ['channelId'],
                'unique',
                'targetAttribute' => ['channelId', 'userId', 'role'],
                'message' => 'User already in chat'
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
            'channelId' => Yii::t('app', 'Channel ID'),
            'userId' => Yii::t('app', 'User ID'),
            'role' => Yii::t('app', 'Role'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'user',
            'role'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getChannel(): ?ActiveQuery
    {
        return $this->hasOne(Channel::className(), ['id' => 'channelId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ?ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @inheritdoc
     * @return ChannelUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChannelUserQuery(get_called_class());
    }
}
