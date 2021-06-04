<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\components\centrifugo\Message;
use common\models\Stream\StreamSessionLike;
use RuntimeException;
use yii\base\Event;

class CreateStreamSessionLikeObserver
{
    /**
     * @param Event $event
     */
    public function execute(Event $event)
    {
        /** @var StreamSessionLike $streamSessionLike */
        $streamSessionLike = $event->sender;
        if (!($streamSessionLike instanceof StreamSessionLike)) {
            throw new RuntimeException('Not StreamSessionLike instance');
        }
        //Notify about create
        $streamSessionLike->notify(Message::ACTION_LIKE_CREATE);
    }
}
