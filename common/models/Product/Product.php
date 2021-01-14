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
use yii\helpers\Json;

/**
 * This is the model class for table "shop".
 * @property int $id         [int(10) unsigned]
 * @property string $externalId [varchar(255)]
 * @property int $shopId     [int(11) unsigned]
 * @property string $title      [varchar(255)]
 * @property string $options    [json]
 * @property string $photo      [varchar(255)]
 * @property string $link       [varchar(255)]
 * @property int $status     [tinyint(3)]
 * @property int $createdAt  [int(11) unsigned]
 * @property int $updatedAt  [int(11) unsigned]
 */
class Product extends ActiveRecord
{
    public const STATUS_HIDDEN = 1;
    public const STATUS_DISPLAYED = 2;
    public const STATUS_PRESENTED = 3;
    public const STATUS_DELETED = 4;
    
    public const PRICE = 'price';
    public const COLOR = 'color';
    public const SIZE = 'size';
    
    public $options;
    
    /**
     * Status Names
     */
    public const STATUSES = [
        self::STATUS_HIDDEN => 'Hidden',
        self::STATUS_DISPLAYED => 'Displayed in the widget',
        self::STATUS_PRESENTED => 'Presented now',
        self::STATUS_DELETED => 'Deleted',
    ];
    
    /**
     * Status codes
     */
    public const STATUSES_CODES = [
        self::STATUS_HIDDEN,
        self::STATUS_DISPLAYED,
        self::STATUS_PRESENTED,
        self::STATUS_DELETED,
    ];
    
    /**
     * fields in option field
     */
    public const OPTION = [
        self::PRICE,
        self::COLOR,
        self::SIZE
    ];
    
    /**
     * required fields in option field
     */
    public const OPTION_REQUIRED = [
        self::PRICE,
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
            [['options'], 'array'],
            [['options'], 'validOption'],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
        ];
    }
    
    /**
     * validate if field has type json
     * @param $attribute
     */
    protected function validOption($attribute): void
    {
        $options = Json::decode($this->options);
        if (!\is_array($options)) {
            $this->addError(
                $attribute,
                'options property has wrong data'
            );
        }
        
        foreach ($options as $product) {
            if (\is_array($product)) {
                $this->isJsonContainsValues($product, $attribute);
                $this->isValidJsonValues($product, $attribute);
            } else {
                $this->addError(
                    $attribute,
                    'options has wrong data structure'
                );
            }
        }
    }
    
    /**
     * @param array $field
     * @param $attribute
     */
    private function isJsonContainsValues(array $field, $attribute): void
    {
        $result = array_diff(self::OPTION_REQUIRED, array_flip($field));
        if (!empty($result)) {
            $notExists = implode(', ', $result);
            $this->addError(
                $attribute,
                "options must have {$notExists} values"
            );
        }
    }
    
    /**
     * @param array $field
     * @param $attribute
     */
    private function isValidJsonValues(array $field, $attribute): void
    {
        foreach (self::OPTION as $optionValue) {
            if (isset($field[$optionValue])) {
                if (is_numeric($field[$optionValue]) && self::PRICE === $optionValue) {
                    $this->addError(
                        $attribute,
                        "{$optionValue} must be number type"
                    );
                } elseif (\is_string($field[$optionValue]) && \strlen($field[$optionValue]) > 255) {
                    $this->addError(
                        $attribute,
                        "{$optionValue} must be lower than 255"
                    );
                } else {
                    $this->addError(
                        $attribute,
                        "{$optionValue} not valid value"
                    );
                }
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
