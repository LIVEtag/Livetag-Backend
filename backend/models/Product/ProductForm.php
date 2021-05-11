<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace backend\models\Product;

use common\models\Product\ProductMedia;
use Throwable;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ProductForm extends Model
{
    const SCENARIO_CREATE = 'create';

    /** @var int */
    public $shopId;

    /** @var string */
    public $externalId;

    /** @var string */
    public $title;

    /** @var string */
    public $description;

    /** @var string */
    public $link;

    /** @var UploadedFile[] */
    public $files;

    /** @var Product */
    public $product;

    /**
     * ProductForm constructor.
     * @param Product|null $product
     * @param array $config
     */
    public function __construct(Product $product = null, $config = [])
    {
        if ($product) {
            $this->setAttributes($product->getAttributes());
        }

        $this->product = $product ?: new Product();
        $this->product->scenario = Product::SCENARIO_MANUALLY;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['externalId', 'title', 'link', 'shopId'], 'required'],
            [['files'], 'required', 'on' => self::SCENARIO_CREATE],
            [['externalId', 'title', 'link', 'description'], 'string', 'max' => 255],
            [
                ['description'],
                'filter',
                'filter' => function ($value) {
                    return $value == '' ? null : $value;
                }
            ],
            ['files', 'file', 'skipOnEmpty' => true, 'maxFiles' => Product::MAX_NUMBER_OF_IMAGES],
            [
                'files',
                'each',
                'rule' => [
                    'image',
                    'mimeTypes' => ProductMedia::getMimeTypes(),
                    'extensions' => ['jpeg', 'png', 'jpg'],
                    'maxSize' => Yii::$app->params['maxUploadImageSize'],
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'externalId' => Yii::t('app', 'ID of the product in the shop (external)'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'link' => Yii::t('app', 'Link'),
            'files' => Yii::t('app', 'Photos'),
        ];
    }

    /**
     * @return bool
     * @throws Throwable
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->product->setAttributes($this->getAttributes());
            $this->product->addOption(['sku' => 'test', 'price' => '0']);
            if (!$this->product->save()) {
                $this->addErrors($this->product->getErrors());
                $transaction->rollBack();
                return false;
            }
            if (!$this->uploadFiles()) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            return true;
        } catch (Throwable $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * Upload files to s3
     * @return boolean
     */
    protected function uploadFiles()
    {
        $oldMedias = $this->product->productMedias;
        foreach ($this->files as $file) {
            if ($file instanceof UploadedFile) {
                if (!$this->uploadFile($file)) {
                    return false;
                }
            }
        }

        // Remove old files only if we upload new files
        if (!empty($this->files)) {
            foreach ($oldMedias as $oldMedia) {
                $oldMedia->delete();
            }
        }

        return true;
    }

    /**
     * Upload single file and store it as media
     * @param UploadedFile $uploadedFile
     * @return bool
     * @throws Throwable
     */
    protected function uploadFile(UploadedFile $uploadedFile): bool
    {
        $media = new ProductMedia();
        $media->setFile($uploadedFile);
        if (!$media->saveFile()) {
            $this->addErrors($media->getErrors());
            return false;
        }
        $media->productId = $this->product->id;
        if (!$media->save()) {
            $this->addErrors($media->getErrors());
            return false;
        }

        return true;
    }
}
