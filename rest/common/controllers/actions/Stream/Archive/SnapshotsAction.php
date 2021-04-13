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
        $streamSession = StreamSession::find()->byId($id)->archived()->published()->one();
        if (!$streamSession) {
            throw new NotFoundHttpException('Stream Session was not found.');
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
        $createdAt = 0;
        $i = 0;
        /** @var StreamSessionProductEvent $event */
        foreach ($query->each() as $event) {
            if (($createdAt !== $event->createdAt)) {
                $snapshots[$i]['timestamp'] = $event->createdAt - $streamSession->startedAt;
                $createdAt = $event->createdAt;
                $i++;
            }
            $currProduct['productId'] = $event->productId;
            $currProduct['status'] = $event->payload['status'] ?? null;
            $snapshots[$i - 1]['products'][] = $currProduct;
        }

        return $snapshots;
    }
}
