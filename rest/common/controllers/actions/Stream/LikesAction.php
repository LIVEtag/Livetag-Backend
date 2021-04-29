<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionLike;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\rest\Action;
use yii\web\NotFoundHttpException;

class LikesAction extends Action
{
    /**
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function run(int $id): array
    {
        /** @var StreamSession $streamSession */
        $streamSession = $this->findModel($id);
        if (!$streamSession->isArchived()) {
            throw new NotFoundHttpException('Archived Stream Session was not found.');
        }

        if (!$streamSession->startedAt || !$streamSession->stoppedAt) {
            throw new NotFoundHttpException('Archived Stream Session with start and stop was not found.');
        }

        return $this->getLikes($streamSession);
    }

    /**
     * @param StreamSession $streamSession
     * @return array
     */
    private function getLikes(StreamSession $streamSession): array
    {
        $likes = StreamSessionLike::find()
            ->select(["`createdAt` - {$streamSession->startedAt} as timestamp", 'COUNT(`id`) as count'])
            ->byStreamSessionId($streamSession->id)
            ->betweenTimestamps($streamSession->startedAt, $streamSession->stoppedAt)
            ->orderBy(['`createdAt`' => SORT_ASC])
            ->groupBy(['`createdAt`'])
            ->asArray()
            ->all();

        foreach ($likes as &$like) {
            $like['timestamp'] = (int)$like['timestamp'];
            $like['count'] = (int)$like['count'];
        }

        $provider = new ArrayDataProvider([
            'allModels' => $likes,
        ]);

        return $provider->getModels();
    }
}
