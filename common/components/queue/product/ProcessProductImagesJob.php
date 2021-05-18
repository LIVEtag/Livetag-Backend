<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\queue\product;

use common\helpers\FileHelper;
use common\helpers\LogHelper;
use common\models\Product\Product;
use common\models\Product\ProductMedia;
use Throwable;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\queue\JobInterface;

/**
 * Save images to s3 and create previews
 * Class ProcessProductImagesJob
 */
class ProcessProductImagesJob extends BaseObject implements JobInterface
{
    /**
     * Product id
     * @var int
     */
    public $id;

    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'product-images';

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        LogHelper::info('Process images for product', self::LOG_CATEGORY, [], $this->logTags());

        $product = Product::find()->byId($this->id)->one();
        if (!$product) {
            LogHelper::error('Product not found', self::LOG_CATEGORY, [], $this->logTags());
            return;
        }
        if (!$product->isInQueue()) {
            LogHelper::error(
                'Invalid status to start product processing',
                self::LOG_CATEGORY,
                LogHelper::extraForModelError($product),
                $this->logTags()
            );
            return;
        }
        // Set processing
        $product->setProcessing();
        $product->save(false, ['status', 'updatedAt']);

        // Process images
        try {
            $this->saveImages($product);
        } catch (Throwable $ex) {
            LogHelper::error('Failed to process Product images', self::LOG_CATEGORY, LogHelper::extraForException($product, $ex), $this->logTags());
            $product->setFailed();
            $product->save(false, ['status', 'updatedAt']);
            return;
        }

        // Save url to model and change status
        $product->setActive();
        if (!$product->save(true, ['status', 'updatedAt'])) {
            LogHelper::error(
                'Failed to save product',
                self::LOG_CATEGORY,
                LogHelper::extraForModelError($product),
                $this->logTags()
            );
        }
        LogHelper::info('Process images processed', self::LOG_CATEGORY, [], $this->logTags());
    }

    /**
     * Save each image to s3 and create preview
     *
     * If one of images failed to save -skip it and process other
     *
     * @param Product $product
     */
    protected function saveImages(Product $product)
    {
        $oldMedias = $product->productMedias;
        $newMedia = [];
        foreach ($product->photos as $photo) {
            try {
                // check photo exist
                if (!FileHelper::fileFromUrlExists($photo)) {
                    LogHelper::error(
                        'Photo not exist',
                        self::LOG_CATEGORY,
                        [
                            'model' => Json::encode($product->toArray(), JSON_PRETTY_PRINT),
                            'photo' => $photo
                        ],
                        $this->logTags()
                    );
                    break;
                }

                $productMedia = new ProductMedia(['productId' => $product->id]);
                $productMedia->setFileFromUrl($photo);
                if (!$productMedia->saveFile() || !$productMedia->save()) {
                    LogHelper::error(
                        'Failed to save product media',
                        self::LOG_CATEGORY,
                        LogHelper::extraForModelError($productMedia),
                        $this->logTags()
                    );
                }
                $newMedia[] = $productMedia;
            } catch (Throwable $ex) {
                LogHelper::error(
                    'Failed to process product media',
                    self::LOG_CATEGORY,
                    LogHelper::extraForException($product, $ex),
                    $this->logTags()
                );
            }
        }

        $product->populateRelation(Product::REL_PRODUCT_MEDIA, $newMedia);
        foreach ($oldMedias as $oldMedia) {
            $oldMedia->delete();
        }
    }

    /**
     * log tags (set id)
     * @return array
     */
    protected function logTags()
    {
        return [LogHelper::TAG_PRODUCT_ID => $this->id];
    }
}
