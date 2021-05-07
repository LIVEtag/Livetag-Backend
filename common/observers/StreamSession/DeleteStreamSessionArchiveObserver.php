<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\StreamSession;

use common\helpers\LogHelper;
use common\models\Analytics\StreamSessionEvent;
use common\models\Analytics\StreamSessionStatistic;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionArchive;
use common\models\Stream\StreamSessionLike;
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

        $streamSession = $streamSessionArchive->streamSession;
        if ($streamSession->isArchived()) {
            $streamSession->status = StreamSession::STATUS_STOPPED;
            if (!$streamSession->save()) {
                LogHelper::error(
                    'Failed to save Stream Session',
                    StreamSession::LOG_CATEGORY,
                    LogHelper::extraForModelError($streamSession)
                );
            }
        }

        // Remove archived views
        $streamSessionStatistic = $streamSession->streamSessionStatistic;
        if ($streamSessionStatistic) {
            $deleteEventsQuery = StreamSessionEvent::getArchivedEventsQuery($streamSession);
            /** @var StreamSessionEvent $archivedEvent */
            foreach ($deleteEventsQuery->each() as $archivedEvent) {
                $archivedEvent->delete();
            }
            StreamSessionStatistic::recalculate($streamSession->getId(), StreamSessionStatistic::ATTR_ARCHIVE_VIEWS_COUNT);
        }
        // Remove archived likes
        $deleteLikeQuery = StreamSessionLike::find()
            ->byStreamSessionId($streamSession->getId());
        if ($streamSession->getStoppedAt()) {
            $deleteLikeQuery->afterTimestamp($streamSession->getStoppedAt());
        }
        /** @var StreamSessionArchive $archivedLike */
        foreach ($deleteLikeQuery->each() as $archivedLike) {
            $archivedLike->delete();
        }
    }
}
