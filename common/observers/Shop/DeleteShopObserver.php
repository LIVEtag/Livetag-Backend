<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\Shop;

use common\models\Shop\Shop;
use RuntimeException;
use Throwable;
use yii\base\Event;

class DeleteShopObserver
{

    /**
     * @param Event $event
     * @throws Throwable
     */
    public function execute(Event $event)
    {
        if (!($event->sender instanceof Shop)) {
            throw new RuntimeException();
        }
        $shop = $event->sender;
        //remove sellers from shop
        foreach ($shop->users as $seller) {
            $seller->delete();
        }
        //remove stream sessions
        foreach ($shop->streamSessions as $streamSession) {
            $streamSession->delete();
        }
        //remove shop products
        foreach ($shop->products as $product) {
            $product->hardDelete();
        }
    }
}
