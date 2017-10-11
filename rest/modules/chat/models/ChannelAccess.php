<?php

namespace rest\modules\chat\models;

use Yii;
use rest\common\models\User;

/**
 * This is the model class for table "channel_access".
 *
 * @property integer $id
 * @property integer $channel_id
 * @property integer $user_id
 * @property integer $role
 *
 * @property Channel $channel
 * @property User $user
 */
class ChannelAccess extends \yii\db\ActiveRecord
{
    
    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_access';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['channel_id', 'user_id'], 'required'],
            [['channel_id', 'user_id', 'role'], 'integer'],
            [['channel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Channel::className(), 'targetAttribute' => ['channel_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('chat', 'ID'),
            'channel_id' => Yii::t('chat', 'Channel ID'),
            'user_id' => Yii::t('chat', 'User ID'),
            'role' => Yii::t('chat', 'Role'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(Channel::className(), ['id' => 'channel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return ChannelAccessQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChannelAccessQuery(get_called_class());
    }
}
