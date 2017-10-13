<?php
namespace rest\modules\chat\models;

use Yii;
use rest\common\models\User;

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
class ChannelUser extends \yii\db\ActiveRecord
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
    public static function tableName()
    {
        return 'channel_user';
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
            [['channel_id'], 'unique', 'targetAttribute' => ['channel_id', 'user_id', 'role'], 'message' => 'User already in chat'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
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
    public function fields()
    {
        return [
            'user' => function () {
                return $this->getUserIdAndName();
            },
            'role'
        ];
    }

    /**
     * centrifugo comp. format
     * @return type
     */
    public function getUserIdAndName()
    {
        return Yii::$app->getModule('chat')->centrifugo::formatUser($this->user);
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
     * @return ChannelUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChannelUserQuery(get_called_class());
    }
}
