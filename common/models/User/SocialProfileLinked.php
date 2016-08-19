<?php

namespace common\models\User;

use Yii;

/**
 * This is the model class for table "user_social_profile_linked".
 *
 * @property integer $user_id
 * @property integer $user_social_profile_id
 */
class SocialProfileLinked extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_social_profile_linked}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_social_profile_id'], 'required'],
            [['user_id', 'user_social_profile_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_social_profile_id' => 'User Social Profile ID',
        ];
    }
}
