<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models;

use yii\db\ActiveQuery;
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
     * One week
     */
    const REMEMBER_TIME = 604800;

    /**
     * One hour
     */
    const NOT_REMEMBER_TIME = 3600;

    const YES_VALUE = 'yes';

    const NO_VALUE = 'no';

    const IS_REMEMBER_FIELD = 'is_remember';

    const IS_VERIFY_IP_FIELD = 'is_verify_ip';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%access_token}}';
    }

    /**
     * @return ActiveQuery|null
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
