<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\helpers\LogHelper;
use common\models\Analytics\StreamSessionEvent;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionSubscriberTokenCreatedEvent;
use RuntimeException;
use yii\web\IdentityInterface;

class SubscriberTokenCreatedObserver
{

    /**
     * @param StreamSessionSubscriberTokenCreatedEvent $event
     * @throws RuntimeException
     */
    public function execute(StreamSessionSubscriberTokenCreatedEvent $event)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $event->sender;
        if (!($streamSession instanceof StreamSession)) {
            throw new RuntimeException('Not StreamSession instance');
        }
        $this->saveEvent($streamSession, $event->user);
    }

    /**
     * @param StreamSession $streamSession
     * @param IdentityInterface $user
     */
    protected function saveEvent(StreamSession $streamSession, IdentityInterface $user)
    {
        /** @var StreamSessionEvent $event */
        $streamEvent = new StreamSessionEvent();
        $streamEvent->userId = $user->getId();
        $streamEvent->streamSessionId = $streamSession->getId();
        $streamEvent->type = StreamSessionEvent::TYPE_VIEW;
        if (!$streamEvent->save()) {
            LogHelper::error('Failed to save StreamSessionEvent', StreamSession::LOG_CATEGORY, LogHelper::extraForModelError($streamEvent));
        }
    }
}
