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
 * @property integer $channel_id
 * @property integer $user_id
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
            [['channel_id', 'user_id'], 'required'],
            [['channel_id', 'user_id', 'role'], 'integer'],
            [
                ['channel_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Channel::class,
                'targetAttribute' => ['channel_id' => 'id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
            [
                ['channel_id'],
                'unique',
                'targetAttribute' => ['channel_id', 'user_id', 'role'],
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
            'channel_id' => Yii::t('app', 'Channel ID'),
            'user_id' => Yii::t('app', 'User ID'),
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
        return $this->hasOne(Channel::className(), ['id' => 'channel_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ?ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
