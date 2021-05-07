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
 * @property int $id             [int(10) unsigned]
 * @property string $externalId  [varchar(255)]
 * @property int $shopId         [int(11) unsigned]
 * @property string $title       [varchar(255)]
 * @property string $description [varchar(255)]
 * @property array $options      [json]
 * @property string $photo       [varchar(255)]
 * @property string $link        [varchar(255)]
 * @property int $status         [tinyint(3)]
 * @property int $createdAt      [int(11) unsigned]
 * @property int $updatedAt      [int(11) unsigned]
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
     * required field price in optionOnly files with these types are allowed and headerOnly files with these types are allowed
     */
    const PRICE = 'price';

    /**
     * sku moved to options
     */
    const SKU = 'sku';

    /**
     * external unique id
     */
    const EXTERNAL_ID = 'externalId';

    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const PHOTO = 'photo';
    const LINK = 'link';

    /**
     * required fields in option
     */
    const OPTION_REQUIRED = [
        self::PRICE,
        self::SKU,
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
            [['externalId', 'shopId', 'title', 'photo', 'link'], 'required'],
            [['shopId', 'status'], 'integer'],
            [['shopId'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::class, 'targetAttribute' => ['shopId' => 'id']],
            [['externalId', 'title', 'link', 'photo', 'description'], 'string', 'max' => 255],
            [['link', 'photo'], 'url', 'defaultScheme' => 'https'],
            [['externalId', 'shopId'], 'unique', 'targetAttribute' => ['externalId', 'shopId']],
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
            'externalId' => Yii::t('app', 'External ID'),
            'shopId' => Yii::t('app', 'Shop Id'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'price' => Yii::t('app', 'Price'),
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
            'externalId' => function () {
                return $this->getExternalId();
            },
            'title' => function () {
                return $this->getTitle();
            },
            'description' => function () {
                return $this->getDescription();
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
    public function getExternalId(): ?string
    {
        return $this->externalId ?: null;
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
    public function getDescription(): ?string
    {
        return $this->description ?: null;
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
     */
    public function delete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save(true, ['status']);
    }

    /**
     * Operation required ONLY when shop deleted.
     */
    public function hardDelete()
    {
        return parent::delete();
    }

    /**
     * Get or Create product by shop and SKU
     * @param int $shopId
     * @param string $externalId
     * @return self|null
     */
    public static function getOrCreate(int $shopId, string $externalId): self
    {
        $product = self::find()->byShop($shopId)->byExternalId($externalId)->one();
        return $product ?? new self(['shopId' => $shopId, self::EXTERNAL_ID => $externalId]);
    }

    /**
     * Add new option to existing one
     * @param array $option
     */
    public function addOption(array $option)
    {
        $options = $this->options;
        $options[] = $option;
        $this->options = array_unique($options, SORT_REGULAR);
    }
}
