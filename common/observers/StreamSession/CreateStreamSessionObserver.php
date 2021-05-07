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
use yii\base\Event;

class CreateStreamSessionObserver
{
    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(AfterCommitEvent $event)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $event->sender;
        if (!($streamSession instanceof StreamSession)) {
            throw new RuntimeException('Not StreamSession instance');
        }
        //Notify about create
        $streamSession->notify(Message::ACTION_STREAM_SESSION_CREATE);
    }
}
