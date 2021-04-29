<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\helpers\LogHelper;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionArchive;
use RuntimeException;
use yii\base\Event;

class DeleteStreamSessionArchiveObserver
{
    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var StreamSessionArchive $streamSessionArchive */
        $streamSessionArchive = $event->sender;
        if (!($streamSessionArchive instanceof StreamSessionArchive)) {
            throw new RuntimeException('Not StreamSessionArchive instance');
        }
        if ($streamSessionArchive->streamSession->isArchived()) {
            $streamSessionArchive->streamSession->status = StreamSession::STATUS_STOPPED;
            $streamSessionArchive->streamSession->rotate = StreamSession::ROTATE_0;
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
