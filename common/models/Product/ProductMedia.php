<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\models\Product;

use common\components\behaviors\TimestampBehavior;
use common\components\FileSystem\format\FileFormatInterface;
use common\components\FileSystem\format\FileFormatTrait;
use common\components\FileSystem\format\FormatEnum;
use common\components\FileSystem\format\formatter\Resize;
use common\components\FileSystem\media\MediaInterface;
use common\components\FileSystem\media\MediaTrait;
use common\components\FileSystem\media\MediaTypeEnum;
use common\components\validation\validators\ArrayValidator;
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
class ProductMedia extends ActiveRecord implements MediaInterface, FileFormatInterface
{
    use MediaTrait;
    use FileFormatTrait;

    /** @see getProduct() */
    const REL_PRODUCT = 'product';

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%product_media}}';
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
    public function rules(): array
    {
        return [
            [['productId', 'type', 'path', 'originName', 'size'], 'required'],
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
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'url',
            'type',
            'formatted' => 'formattedUrls'
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
     * @inheridDoc
     */
    public static function getFormatters(): array
    {
        return [
            FormatEnum::SMALL => [
                'class' => Resize::class,
                'width' => 150,
                'height' => 200,
            ],
            FormatEnum::LARGE => [
                'class' => Resize::class,
                'width' => 960,
                'height' => 1280,
            ]
        ];
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
