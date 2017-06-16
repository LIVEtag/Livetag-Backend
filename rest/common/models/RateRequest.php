<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models;

use yii\db\ActiveRecord;

/**
 * Class RateRequest
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
            [
                ['action_type', 'user_id'],
                'required'
            ]
        ];
    }
}