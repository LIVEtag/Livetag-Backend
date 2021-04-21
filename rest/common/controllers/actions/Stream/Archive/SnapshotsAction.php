<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream\Archive;

use common\models\Analytics\StreamSessionProductEvent;
use common\models\Product\StreamSessionProduct;
use common\models\Stream\StreamSession;
use yii\helpers\ArrayHelper;
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

        if (!$streamSession->sessionId) {
            return $this->getProducts($streamSession);
        }

        return $this->getSnapshots($streamSession);
    }

    /**
     * @param StreamSession $streamSession
     * @return array
     */
    private function getProducts(StreamSession $streamSession): array
    {
        $products = StreamSessionProduct::find()
            ->select(['`productId`', '`status`'])
            ->byStreamSessionId($streamSession->id)
            ->all();

        if (empty($products)) {
            return [];
        }

        return [
            'timestamp' => 0,
            'products' => ArrayHelper::toArray($products),
        ];
    }

    /**
     * @param StreamSession $streamSession
     * @return array
     */
    private function getSnapshots(StreamSession $streamSession): array
    {
        $query = StreamSessionProductEvent::find()
            ->select(['`payload`', '`productId`', '`createdAt`', '`type`'])
            ->byStreamSessionId($streamSession->id)
            ->byProductTypes()
            ->orderBy(['`createdAt`' => SORT_ASC]);

        $snapshots = [];
        $productsById = [];
        $i = 0;
        /** @var StreamSessionProductEvent $event */
        foreach ($query->each() as $event) {
            $timestamp = $event->createdAt - $streamSession->startedAt;
            if ($event->type !== StreamSessionProductEvent::TYPE_PRODUCT_DELETE) {
                $productsById[$event->productId] = [
                    'productId' => $event->productId,
                    'status' => $event->payload['status'] ?? null,
                ];
            } elseif (!empty($productsById[$event->productId])) {
                unset($productsById[$event->productId]);
            }

            if (isset($snapshots[$i]['timestamp']) && ($snapshots[$i]['timestamp'] != $timestamp)) {
                $i++;
            }

            $snapshots[$i]['timestamp'] = $timestamp;
            $snapshots[$i]['products'] = array_values($productsById);
        }

        return $snapshots;
    }
}
