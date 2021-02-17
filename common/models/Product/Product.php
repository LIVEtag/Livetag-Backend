<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Product;

use common\components\behaviors\TimestampBehavior;
use common\components\validation\validators\OptionValidator;
use common\models\queries\Product\ProductQuery;
use common\models\Shop\Shop;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 * @property int $id         [int(10) unsigned]
 * @property string $sku     [varchar(255)]
 * @property int $shopId     [int(11) unsigned]
 * @property string $title      [varchar(255)]
 * @property array $options    [json]
 * @property string $photo      [varchar(255)]
 * @property string $link       [varchar(255)]
 * @property int $status     [tinyint(3)]
 * @property int $createdAt  [int(11) unsigned]
 * @property int $updatedAt  [int(11) unsigned]
 */
class Product extends ActiveRecord implements ProductInterface
{
    /**
     * Removed product. required for analytics
     */
    const STATUS_DELETED = 0;

    /**
     * Default active status
     */
    const STATUS_ACTIVE = 10;

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DELETED => 'Deleted',
    ];
    
    /**
     * required field price in option and header
     */
    const PRICE = 'price';
    
    /**
     * required fields in option
     */
    const OPTION_REQUIRED = [
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
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find(): ProductQuery
    {
        return new ProductQuery(get_called_class());
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
            [['sku', 'shopId', 'title'], 'required'],
            [['shopId', 'status'], 'integer'],
            [['shopId'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::class, 'targetAttribute' => ['shopId' => 'id']],
            [['sku', 'title', 'link', 'photo'], 'string', 'max' => 255],
            [['sku', 'shopId'], 'unique', 'targetAttribute' => ['sku', 'shopId']],
            ['options', 'each', 'rule' => [OptionValidator::class]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'price' => Yii::t('app', 'Price'),
            'sku' => Yii::t('app', 'SKU'),
            'shopId' => Yii::t('app', 'Shop Id'),
            'photo' => Yii::t('app', 'Photo'),
            'status' => Yii::t('app', 'Status'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id' => function () {
                return $this->getId();
            },
            'sku' => function () {
                return $this->getSku();
            },
            'title' => function () {
                return $this->getTitle();
            },
            'photo' => function () {
                return $this->getPhoto();
            },
            'link' => function () {
                return $this->getLink();
            },
            'options' => function () {
                return $this->getOptions();
            },
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
     * @inheritdoc
     */
    public function getId(): ?int
    {
        return $this->id ? (int) $this->id : null;
    }

    /**
     * @inheritdoc
     */
    public function getSku(): ?string
    {
        return $this->sku ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getShopId(): ?int
    {
        return $this->shopId ? (int) $this->shopId : null;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): ?string
    {
        return $this->title ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getPhoto(): ?string
    {
        return $this->photo ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getLink(): ?string
    {
        return $this->link ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(): array
    {
        return $this->options ?: [];
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): ?int
    {
        return $this->status ? (int) $this->status : null;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt ? (int) $this->createdAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): ?int
    {
        return $this->createdAt ? (int) $this->createdAt : null;
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
