<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\components\db\AfterCommitEvent;
use common\helpers\LogHelper;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionArchive;
use RuntimeException;

class UpdateStreamSessionArchiveObserver
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
        if (isset($event->changedAttributes['status']) && $streamSessionArchive->isReady() && $streamSessionArchive->streamSession->isStopped()) {
            $streamSessionArchive->streamSession->status = StreamSession::STATUS_ARCHIVED;
            if (!$streamSessionArchive->streamSession->save()) {
                LogHelper::error(
                    'Failed to save Stream Session',
                    StreamSession::LOG_CATEGORY,
                    LogHelper::extraForModelError($streamSessionArchive->streamSession)
                );
            }
        }
    }
}
