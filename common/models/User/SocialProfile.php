<?php

namespace common\models\User;

use Yii;
use yii\behaviors\TimestampBehavior;
use rest\common\models\User;

/**
 * This is the model class for table "user_social_profile".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $type
 * @property string $socialId
 * @property string $email
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property User $user
 */
class SocialProfile extends \yii\db\ActiveRecord
{
    const TYPE_GOOGLE = 1;
    const TYPE_LINKEDIN = 2;
    const TYPE_FACEBOOK = 3;
    const TYPE_TWITTER = 4;

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
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'type', 'socialId', 'email'], 'required'],
            [['userId', 'type', 'createdAt', 'updatedAt'], 'integer'],
            [['socialId', 'email'], 'string', 'max' => 255],
            [
                ['userId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['userId' => 'id']
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
            'userId' => 'User ID',
            'type' => 'Type',
            'socialId' => 'Social ID',
            'email' => 'Email',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     *
     *
     * @param string $socialId
     * @return
     */
    public static function findBySocialId($socialId)
    {
        return static::find()
            ->andWhere('socialId = :socialId', ['socialId' => $socialId])
            ->one();
    }
}
