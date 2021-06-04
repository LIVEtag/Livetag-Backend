<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSessionProduct;

use common\components\centrifugo\Message;
use common\models\Analytics\StreamSessionProductEvent;
use common\models\Product\StreamSessionProduct;
use RuntimeException;
use yii\base\Event;

class CreateStreamSessionProductObserver
{
    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var StreamSessionProduct $streamSessionProduct */
        $streamSessionProduct = $event->sender;
        if (!($streamSessionProduct instanceof StreamSessionProduct)) {
            throw new RuntimeException('Not StreamSessionProduct instance');
        }
        //Notify about create
        $streamSessionProduct->notify(Message::ACTION_STREAM_SESSION_PRODUCT_CREATE);
        $streamSession = $streamSessionProduct->streamSession;
        if ($streamSession && $streamSession->isActive()) {
            $streamSessionProduct->saveEventToDatabase(StreamSessionProductEvent::TYPE_PRODUCT_CREATE);
        }
    }
}
