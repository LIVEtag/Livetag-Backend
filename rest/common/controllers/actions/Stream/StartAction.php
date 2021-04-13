<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionToken;
use rest\common\models\views\StreamSession\UpdateStreamSession;
use Yii;
use yii\rest\Action;
use yii\web\ServerErrorHttpException;

class StartAction extends Action
{

    /**
     * @param int $id
     * @return StreamSessionToken|UpdateStreamSession
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\NotFoundHttpException
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
        /** @var UpdateStreamSession $updateStreamSession */
        $updateStreamSession = Yii::createObject(UpdateStreamSession::class, [$streamSession]);
        $updateStreamSession->setAttributes(Yii::$app->request->getBodyParams());

        if (!$updateStreamSession->update() && !$updateStreamSession->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update Stream Session.');
        }

        if ($updateStreamSession->hasErrors()) {
            return $updateStreamSession;
        }

        return $streamSession->start();
    }
}
