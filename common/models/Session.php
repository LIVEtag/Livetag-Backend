<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "session".
 *
 * @property string $id
 * @property integer $expire
 * @property integer $userId
 * @property resource $data
 */
class Session extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%session}}';
    }
    
    
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'expire'], 'required'],
            [['expire', 'userId'], 'integer'],
            [['data'], 'string'],
            [['id'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'expire' => Yii::t('app', 'Expire'),
            'userId' => Yii::t('app', 'User ID'),
            'data' => Yii::t('app', 'Data'),
        ];
    }
}
