<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\helpers\LogHelper;
use common\models\Stream\StreamSession;
use Yii;
use yii\rest\Action;
use yii\web\BadRequestHttpException;

class StopAction extends Action
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

        if (!$streamSession->stop()) {
            LogHelper::error('Failed to Stop session', StreamSession::LOG_CATEGORY, LogHelper::extraForModelError($streamSession));
            throw new BadRequestHttpException(Yii::t('app', 'Failed to Stop session for unknown reason'));
        }

        Yii::$app->response->setStatusCode(204);
    }
}
