<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\Product;

use common\components\centrifugo\Message;
use common\components\db\AfterCommitEvent;
use common\models\Product\Product;
use RuntimeException;

class DeleteProductObserver
{

    /**
     * @param AfterCommitEvent $event
     * @throws RuntimeException
     */
    public function execute(AfterCommitEvent $event)
    {
        /** @var Product $product */
        $product = $event->sender;
        if (!($product instanceof Product)) {
            throw new RuntimeException('Not Product instance');
        }

        //send dlete event
        $product->notify(Message::ACTION_PRODUCT_DELETE);
    }
}
