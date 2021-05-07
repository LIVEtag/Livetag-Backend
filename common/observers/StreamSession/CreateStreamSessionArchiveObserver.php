<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\components\db\AfterCommitEvent;
use common\models\Stream\StreamSessionArchive;
use RuntimeException;

class CreateStreamSessionArchiveObserver
{

    /**
     * @param AfterCommitEvent $event
     * @throws RuntimeException
     */
    public function execute(AfterCommitEvent $event)
    {
        /** @var StreamSessionArchive $streamSessionArchive */
        $streamSessionArchive = $event->sender;
        if (!($streamSessionArchive instanceof StreamSessionArchive)) {
            throw new RuntimeException('Not StreamSessionArchive instance');
        }
        //Send archive to queue for processing
        $streamSessionArchive->sendToQueue();
    }
}
