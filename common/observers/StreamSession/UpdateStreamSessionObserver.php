<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\components\centrifugo\Message;
use common\components\db\AfterCommitEvent;
use common\models\Stream\StreamSession;
use RuntimeException;

class UpdateStreamSessionObserver
{
    /**
     * @param AfterCommitEvent $event
     * @throws RuntimeException
     */
    public function execute(AfterCommitEvent $event)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $event->sender;
        if (!($streamSession instanceof StreamSession)) {
            throw new RuntimeException('Not StreamSession instance');
        }
        $streamSession->notify(Message::ACTION_STREAM_SESSION_UPDATE);
        if (isset($event->changedAttributes['status']) && $streamSession->isActive()) {
            $streamSession->saveProductEventsToDatabase();
        }
    }
}
