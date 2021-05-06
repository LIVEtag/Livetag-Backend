<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\Analytics;

use common\models\Analytics\StreamSessionProductEvent;
use common\models\Analytics\StreamSessionStatistic;
use LogicException;
use RuntimeException;
use yii\base\Event;

class CreateStreamSessionProductEventObserver
{

    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var StreamSessionProductEvent $event */
        $productEvent = $event->sender;
        if (!($productEvent instanceof StreamSessionProductEvent)) {
            throw new RuntimeException('Not StreamSessionProductEvent instance');
        }
        switch ($productEvent->type) {
            case StreamSessionProductEvent::TYPE_ADD_TO_CART:
                $streamSession = $productEvent->streamSession;
                if ($streamSession->isNew()) {
                    throw new LogicException('No event type for new session');
                }
                $event = $streamSession->isActive() ?
                    StreamSessionStatistic::ATTR_STREAM_ADD_TO_CART_COUNT :
                    StreamSessionStatistic::ATTR_ARCHIVE_ADD_TO_CART_COUNT;
                StreamSessionStatistic::recalculate($productEvent->getStreamSessionId(), $event);
                break;
        }
    }
}
