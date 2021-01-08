<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models;

use common\components\behaviors\TimestampBehavior;
use common\models\queries\SessionQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "session".
 * @property integer $id
 * @property integer $userId
 * @property int    $expire [int(11) unsigned]
 * @property string $data   [blob]
 * @property string $agent  [varchar(255)]
 * @property string $ip     [varchar(255)]
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
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }
    
    /**
     * @inheritdoc
     * @return SessionQuery the active query used by this AR class.
     */
    public static function find(): SessionQuery
    {
        return new SessionQuery(get_called_class());
    }
    
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['userId', 'expired'], 'required'],
            [['userId', 'expired'], 'integer'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'expire' => Yii::t('app', 'Expire'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'userId',
            'data',
            'expire',
            'createdAt',
            'expiredAt',
        ];
    }
    
}
