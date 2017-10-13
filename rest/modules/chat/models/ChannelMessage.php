<?php
namespace rest\modules\chat\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use rest\common\models\User;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property integer $channel_id
 * @property integer $user_id
 * @property string $message
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Channel $channel
 * @property User $user
 */
class ChannelMessage extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_message';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['channel_id', 'user_id', 'message'], 'required'],
            [['channel_id', 'user_id'], 'integer'],
            [['message'], 'string', 'max' => 255],
            [['channel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Channel::className(), 'targetAttribute' => ['channel_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'message',
            'date' => function () {
                return $this->created_at;
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [
            'user',
            'channel'
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
            'message' => Yii::t('app', 'Message'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * centrifugo comp. format
     * @return type
     */
    public function getUserIdAndName()
    {
        return Yii::$app->getModule('chat')->centrifugo::formatUser($this->user);
    }

    /**
     * @inheritdoc
     *
     * before save message->publish it to centrifugo channel
     */
    public function beforeSave($insert)
    {
        if (!Yii::$app->getModule('chat')->centrifugo->setUser($this->user)->publishMessage($this->channel->url, $this->message)) {
            $this->addError('channel_id', Yii::t('app', 'Failed to send message to channel'));
            return false;
        }

        return parent::beforeSave($insert);
    }
}
