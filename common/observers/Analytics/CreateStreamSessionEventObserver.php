<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\Analytics;

use common\models\Analytics\StreamSessionEvent;
use common\models\Analytics\StreamSessionStatistic;
use RuntimeException;
use yii\base\Event;

class CreateStreamSessionEventObserver
{

    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var StreamSessionEvent $event */
        $streamEvent = $event->sender;
        if (!($streamEvent instanceof StreamSessionEvent)) {
            throw new RuntimeException('Not StreamSessionEvent instance');
        }
        switch ($streamEvent->type) {
            case StreamSessionEvent::TYPE_VIEW:
                StreamSessionStatistic::recalculate($streamEvent->getStreamSessionId(), StreamSessionStatistic::ATTR_VIEWS_COUNT);
                break;
        }
    }
}
