<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Vonage;

use common\components\queue\stream\SaveArchiveFromWebhookJob;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;

/**
 * Class ArchiveCallbackAction.
 */
class ArchiveCallbackAction extends Action
{
    /**
     * file uploaded to s3
     */
    const ARCHIVE_UPLOADED_STATUS = 'uploaded';

    public function run()
    {
        $data = Yii::$app->request->post();

        $status = ArrayHelper::getValue($data, 'status');
        //we receive callback, but do nothing
        //ignore all other callbacks, but response to server that all ok
        if ($status != self::ARCHIVE_UPLOADED_STATUS) {
            Yii::$app->response->setStatusCode(204, 'Do nothing');
            return;
        }

        //Create job to save video
        $job = new SaveArchiveFromWebhookJob();
        $job->data = $data;
        Yii::$app->queue->push($job);

        Yii::$app->response->setStatusCode(204, 'OK');
    }
}
