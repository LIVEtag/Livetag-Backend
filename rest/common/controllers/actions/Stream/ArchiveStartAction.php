<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use Yii;
use yii\rest\Action;

class ArchiveStartAction extends Action
{

    /**
     * @param int $id
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
        Yii::$app->vonage->startArchiving($streamSession->sessionId, $streamSession->name);
        Yii::$app->response->setStatusCode(204);
    }
}
