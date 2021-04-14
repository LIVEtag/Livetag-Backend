<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream\Archive;

use common\models\Analytics\StreamSessionProductEvent;
use common\models\Stream\StreamSession;
use yii\rest\Action;
use yii\web\NotFoundHttpException;

class SnapshotsAction extends Action
{
    /**
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function run(int $id)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $this->findModel($id);
        if (!$streamSession->isArchived()) {
            throw new NotFoundHttpException('Archived Stream Session was not found.');
        }

        return $this->getSnapshots($streamSession);
    }

    /**
     * @param StreamSession $streamSession
     * @return array
     */
    private function getSnapshots(StreamSession $streamSession)
    {
        $query = StreamSessionProductEvent::find()
            ->select(['`payload`', '`productId`', '`createdAt`'])
            ->byStreamSessionId($streamSession->id)
            ->byProductTypes()
            ->orderBy(['`createdAt`' => SORT_ASC]);

        $snapshots = [];
        $i = 0;
        /** @var StreamSessionProductEvent $event */
        foreach ($query->each() as $event) {
            $timestamp = $event->createdAt - $streamSession->startedAt;
            if (isset($snapshots[$i]['timestamp']) && ($snapshots[$i]['timestamp'] != $timestamp)) {
                $i++;
            }
            $snapshots[$i]['timestamp'] = $timestamp;
            $snapshots[$i]['products'][] = [
                'productId' => $event->productId,
                'status' => $event->payload['status'] ?? null
            ];
        }

        return $snapshots;
    }
}
