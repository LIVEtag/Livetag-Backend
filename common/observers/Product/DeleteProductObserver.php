<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\Product;

use common\components\db\AfterCommitEvent;
use common\models\Product\Product;
use RuntimeException;
use yii\base\Event;

/**
 * Total Product removal (shop delete scenario)
 */
class DeleteProductObserver
{

    /**
     * @param AfterCommitEvent $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var Product $product */
        $product = $event->sender;
        if (!($product instanceof Product)) {
            throw new RuntimeException('Not Product instance');
        }

        //remove cover
        if ($product->productMedias) {
            foreach ($product->productMedias as $media) {
                $media->delete();
            }
        }
    }
}
