<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\components\centrifugo\Message;
use common\models\Stream\StreamSession;
use RuntimeException;
use yii\base\Event;

class EndSoonStreamSessionObserver
{
    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $event->sender;
        if (!($streamSession instanceof StreamSession)) {
            throw new RuntimeException('Not StreamSession instance');
        }
        $streamSession->notify(Message::ACTION_STREAM_SESSION_END_SOON);
    }
}
