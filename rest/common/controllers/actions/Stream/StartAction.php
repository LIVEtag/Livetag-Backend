<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use rest\common\models\views\StreamSession\StartStreamSession;
use Yii;
use yii\rest\Action;

class StartAction extends Action
{

    /**
     * @param int $id
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\db\Exception
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
        /** @var StartStreamSession $updateStreamSession */
        $startStreamSession = Yii::createObject(StartStreamSession::class, [$streamSession]);
        $startStreamSession->setAttributes(Yii::$app->request->getBodyParams());
        $token = $startStreamSession->start();

        if ($startStreamSession->hasErrors()) {
            return $startStreamSession;
        }

        return $token;
    }
}
