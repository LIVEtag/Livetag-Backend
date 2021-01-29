<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Product;

use common\components\behaviors\TimestampBehavior;
use common\models\queries\Product\StreamSessionProductQuery;
use common\models\Stream\StreamSession;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stream_session_product".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property integer $productId
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read Product $product
 * @property-read StreamSession $streamSession
 */
class StreamSessionProduct extends ActiveRecord
{
    const STATUS_DISPLAYED = 2;
    const STATUS_PRESENTED = 3;

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_DISPLAYED => 'Displayed in the widget',
        self::STATUS_PRESENTED => 'Presented now',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session_product}}';
    }

    /**
     * @inheritdoc
     * @return StreamSessionProductQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionProductQuery
    {
        return new StreamSessionProductQuery(get_called_class());
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
     */
    public function rules(): array
    {
        return [
            [['streamSessionId', 'productId', 'status'], 'required'],
            [['streamSessionId', 'productId', 'status'], 'integer'],
            [['streamSessionId', 'productId'], 'unique', 'targetAttribute' => ['streamSessionId', 'productId']],
            ['status', 'default', 'value' => self::STATUS_DISPLAYED],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [['productId'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetRelation' => 'product'],
            [['streamSessionId'], 'exist', 'skipOnError' => true, 'targetClass' => StreamSession::class, 'targetRelation' => 'streamSession'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'streamSessionId' => Yii::t('app', 'Stream Session ID'),
            'productId' => Yii::t('app', 'Product ID'),
            'status' => Yii::t('app', 'Status'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'productId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }
}
