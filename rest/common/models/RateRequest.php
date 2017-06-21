<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models;

use yii\db\ActiveRecord;

/**
 * Class RateRequest
 * @property string action_id
 * @property null|string ip
 * @property null|string user_agent
 * @property int created_at
 * @property int|mixed count
 * @property int last_request
 */
class RateRequest extends ActiveRecord
{
    public static function tableName()
    {
        return parent::tableName();
    }

    public function rules()
    {
        return [
            ['action_id', 'required']
        ];
    }
}