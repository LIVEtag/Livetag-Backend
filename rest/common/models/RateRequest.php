<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class RateRequest
 * @property int id
 * @property string $action_id
 * @property null|string $ip
 * @property null|string $user_agent
 * @property int $created_at
 * @property int|mixed $count
 * @property int $last_request
 */
class RateRequest extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['action_id', 'required']
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'last_request'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_request'],
                ],
            ],
        ];
    }
}
