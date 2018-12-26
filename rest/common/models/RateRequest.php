<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class RateRequest
 * @property int $id
 * @property string $actionId
 * @property string $ip
 * @property string $userAgent
 * @property int $createdAt
 * @property int $count
 * @property int $lastRequest
 */
class RateRequest extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['actionId', 'ip', 'userAgent'], 'required'],
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
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'lastRequest'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lastRequest'],
                ],
            ],
        ];
    }
}
