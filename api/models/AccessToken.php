<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace api\models;

use yii\db\ActiveRecord;

/**
 * Class AccessToken
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property boolean $is_verify_ip
 * @property string $user_ip
 * @property string $user_agent
 * @property boolean $is_frozen_expire
 * @property integer $created_at
 * @property integer $expired_at
 */
class AccessToken extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%access_token}}';
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->one();
    }
}
