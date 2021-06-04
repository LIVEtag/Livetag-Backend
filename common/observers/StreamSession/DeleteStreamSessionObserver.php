<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\models\Stream\StreamSession;
use RuntimeException;
use Throwable;
use yii\base\Event;

class DeleteStreamSessionObserver
{

    /**
     * Only when show deleted
     * @param Event $event
     * @throws Throwable
     */
    public function execute(Event $event)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $event->sender;
        if (!($streamSession instanceof StreamSession)) {
            throw new RuntimeException('Not StreamSession instance');
        }

        //remove cover
        if ($streamSession->streamSessionCover) {
            $streamSession->streamSessionCover->delete();
        }

        //remove archive
        if ($streamSession->archive) {
            $streamSession->archive->delete();
        }
    }
}
