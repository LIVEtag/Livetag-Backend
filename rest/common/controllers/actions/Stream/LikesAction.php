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
use yii\db\Expression;
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
        if ($this->checkAccess) {
            // phpcs:disable
            call_user_func($this->checkAccess, $this->id, $streamSession);
            // phpcs:enable
        }
        if (!$streamSession->isArchived()) {
            throw new NotFoundHttpException('Archived Stream Session was not found.');
        }

        if (!$streamSession->startedAt || !$streamSession->stoppedAt) {
            return [];
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
            ->select([
                new Expression("`createdAt` - {$streamSession->startedAt} as timestamp"),
                new Expression('COUNT(`id`) as count'),
            ])
            ->byStreamSessionId($streamSession->id)
            ->betweenTimestamps($streamSession->startedAt, $streamSession->stoppedAt)
            ->orderBy(['`createdAt`' => SORT_ASC])
            ->groupBy(['`createdAt`'])
            ->asArray()
            ->all();

        foreach ($likes as &$like) {
            $like = array_map('intval', $like);
        }

        $provider = new ArrayDataProvider([
            'allModels' => $likes,
        ]);

        return $provider->getModels();
    }
}
