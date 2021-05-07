<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\models\Stream;

use common\components\behaviors\TimestampBehavior;
use common\components\FileSystem\media\MediaInterface;
use common\components\FileSystem\media\MediaTrait;
use common\components\FileSystem\media\MediaTypeEnum;
use common\components\validation\validators\ArrayValidator;
use common\models\Product\Product;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%product_media}}".
 *
 * @property integer $id
 * @property integer $productId
 * @property string $path
 * @property string $originName
 * @property integer $size
 * @property array $formatted
 * @property string $type
 * @property integer $createdAt
 *
 * @property-read Product $product
 */
class ProductMedia extends ActiveRecord implements MediaInterface
{
    use MediaTrait;

    /** @see getProduct() */
    const REL_PRODUCT = 'product';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%product_media}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['productId', 'formatted', 'type', 'path', 'originName', 'size'], 'required'],
            [['productId'], 'integer'],
            ['formatted', ArrayValidator::class],
            ['size',  'integer', 'min' => 0],
            ['type', 'in', 'range' => self::getMediaTypes()],
            [['path', 'originName'], 'string', 'max' => 255],
            [
                ['productId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Product::class,
                'targetRelation' => self::REL_PRODUCT,
            ],
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'productId' => Yii::t('app', 'Product ID'),
            'path' => Yii::t('app', 'Path'),
            'originName' => Yii::t('app', 'Origin Name'),
            'size' => Yii::t('app', 'Size'),
            'formatted' => Yii::t('app', 'Formatted'),
            'type' => Yii::t('app', 'Type'),
            'createdAt' => Yii::t('app', 'Created At'),
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
     * Get relative path for file store
     * @return string
     */
    public function getRelativePath(): string
    {
        return 'product-media';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public static function getMediaTypes(): array
    {
        return [
            MediaTypeEnum::TYPE_IMAGE,
        ];
    }

    /**
     * Remove file from s3
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if ($this->path && !$this->deleteFile()) {
            return false;
        }
        return parent::beforeDelete();
    }
}
