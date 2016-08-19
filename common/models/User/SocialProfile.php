<?php

namespace common\models\User;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_social_profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $social_id
 * @property string $email
 * @property integer $created_at
 * @property integer $expired_at
 *
 * @property User $user
 */
class SocialProfile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_social_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'social_id', 'email'], 'required'],
            [['user_id', 'type', 'created_at', 'expired_at'], 'integer'],
            [['social_id', 'email'], 'string', 'max' => 255],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'social_id' => 'Social ID',
            'email' => 'Email',
            'created_at' => 'Created At',
            'expired_at' => 'Expired At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
