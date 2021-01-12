<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Product;

use common\components\behaviors\TimestampBehavior;
use common\models\queries\Shop\ShopQuery;
use common\models\Shop\Shop;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop".
 * @property int $id         [int(10) unsigned]
 * @property string $externalId [varchar(255)]
 * @property int $shopId     [int(11) unsigned]
 * @property string $title      [varchar(255)]
 * @property string $options    [json]
 * @property string $photo      [varchar(255)]
 * @property string $link       [varchar(255)]
 * @property bool $status     [tinyint(3)]
 * @property int $createdAt  [int(11) unsigned]
 * @property int $updatedAt  [int(11) unsigned]
 */
class Product extends ActiveRecord
{
    public const STATUS_HIDDEN = 0;

    public const STATUS_DISPLAYED = 10;
    
    public const STATUS_PRESENTED = 20;
    
    public const STATUS_DELETED = 30;
    
    public $options;
    
    public $price;
    
    public const PRICE = 'price';
    
    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_HIDDEN => 'Hidden',
        self::STATUS_DISPLAYED => 'Displayed in the widget',
        self::STATUS_PRESENTED => 'Presented now',
        self::STATUS_DELETED => 'Deleted',
    ];
    
    /**
     * existing fields in option field
     */
    const OPTION_EXISTS = [
        self::PRICE => self::PRICE,
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%product}}';
    }
    
    /**
     * @inheritdoc
     * @return ShopQuery the active query used by this AR class.
     */
    public static function find(): ShopQuery
    {
        return new ShopQuery(get_called_class());
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
            [['externalId', 'shopId', 'title'], 'required'],
            [['shopId', 'status'], 'integer'],
            [['shopId'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::class, 'targetAttribute' => ['shopId' => 'id']],
            [['externalId', 'title', 'link', 'photo'], 'string', 'max' => 255],
            [['options'], 'validJson'],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
        ];
    }
    
    /**
     * validate if field has type json
     * @param $attribute
     */
    public function validJson($attribute)
    {
        if (!\is_array($this)) {
            $this->addError(
                $attribute,
                'options has wrong type'
            );
        }
        $options = json_decode($this->options, true);
        foreach ($options as $product) {
            $this->checkArrayFieldInJson($product, $attribute);
        }
    }
    
    
    /**
     * @param array $field
     * @param $attribute
     */
    private function checkArrayFieldInJson(array $field, $attribute)
    {
        $optionFields = self::OPTION_EXISTS;
        $result = array_diff(self::OPTION_EXISTS, array_flip($field));
        if (!empty($result)) {
            $notExists = implode(', ', $result);
            $this->addError(
                $attribute,
                "options must have {$notExists} values"
            );
        }
        
        foreach ($optionFields as $optionValue) {
            if ((isset($field[$optionValue]) && \strlen((string)$field[$optionValue]) > 255)) {
                $this->addError(
                    $attribute,
                    "{$optionValue} must be lower than 255"
                );
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'price' => 'Price',
            'externalId' => 'External Id',
            'shopId' => 'Shop Id',
            'photo' => 'Photo',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getShop(): ActiveQuery
    {
        return $this->hasOne(Shop::class, ['id' => 'shopId']);
    }
    
    /**
     * Fake delete
     * @todo: add delete logic
     */
    public function delete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save(true, ['status']);
    }
}
