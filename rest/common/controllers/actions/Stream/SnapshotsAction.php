<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Analytics\StreamSessionProductEvent;
use common\models\Stream\StreamSession;
use yii\data\ArrayDataProvider;
use yii\rest\Action;
use yii\web\NotFoundHttpException;

class SnapshotsAction extends Action
{
    /**
     * @param int $id
     * @return \yii\data\ArrayDataProvider
     * @throws NotFoundHttpException
     */
    public function run(int $id)
    {
        /** @var StreamSession $streamSession */
        $streamSession = StreamSession::find()->byId($id)->archived()->published()->one();
        if (!$streamSession) {
            throw new NotFoundHttpException('Stream Session was not found.');
        }

        return new ArrayDataProvider([
            'allModels' => $this->getSnapshots($streamSession),
        ]);
    }

    /**
     * @param StreamSession $streamSession
     * @return array
     */
    private function getSnapshots(StreamSession $streamSession)
    {
        $events = StreamSessionProductEvent::find()
            ->select(['`payload`', '`productId`', '`createdAt`'])
            ->byStreamSessionId($streamSession->id)
            ->byProductTypes()
            ->orderBy(['`createdAt`' => SORT_ASC])
            ->all();

        $productsByCreatedAt = [];
        /** @var StreamSessionProductEvent $event */
        foreach ($events as $event) {
            $currProducts = [];
            $currProducts['productId'] = $event->productId;
            $currProducts['status'] = $event->payload['status'];
            $productsByCreatedAt[$event->createdAt][] = $currProducts;
        }

        $snapshots = [];
        foreach ($productsByCreatedAt as $createdAt => $products) {
            $currSnapshot = [];
            $currSnapshot['timestamp'] = $createdAt - $streamSession->startedAt;
            $currSnapshot['products'] = $products;
            $snapshots[] = $currSnapshot;
        }

        return $snapshots;
    }
}
