<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionLike;
use Yii;
use yii\rest\Action;
use yii\web\NotFoundHttpException;

class LikeCreateAction extends Action
{
    /**
     * @param int $id
     * @return StreamSessionLike
     * @throws NotFoundHttpException
     */
    public function run(int $id)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $this->findModel($id);
        if ($this->checkAccess) {
            // phpcs:disable
            call_user_func($this->checkAccess, $this->id, $streamSession);
            // phpcs:enable
        }

        if (!$streamSession->isActive() && !$streamSession->isArchived()) {
            throw new NotFoundHttpException('Stream Session was not found.');
        }

        $streamSessionLike = new StreamSessionLike();
        $streamSessionLike->streamSessionId = $streamSession->getId();
        $streamSessionLike->userId = Yii::$app->user->identity->getId();
        $streamSessionLike->save();

        if ($streamSessionLike->hasErrors()) {
            return $streamSessionLike;
        }

        Yii::$app->response->setStatusCode(201);
    }
}
