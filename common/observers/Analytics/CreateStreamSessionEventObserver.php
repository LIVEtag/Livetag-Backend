<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\Analytics;

use common\models\Analytics\StreamSessionEvent;
use common\models\Analytics\StreamSessionStatistic;
use LogicException;
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
                $streamSession = $streamEvent->streamSession;
                if ($streamSession->isNew()) {
                    throw new LogicException('No event type for new session');
                }
                $event = $streamSession->isActive() ?
                    StreamSessionStatistic::ATTR_STREAM_VIEWS_COUNT :
                    StreamSessionStatistic::ATTR_ARCHIVE_VIEWS_COUNT;
                StreamSessionStatistic::recalculate($streamSession->getId(), $event);
                break;
        }
    }
}
