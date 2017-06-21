<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\observers;

use rest\common\models\RateRequest;
use rest\components\api\actions\events\BeforeActionEvent;

/**
 * Class UpdateObserver
 */
class UpdateObserver
{
    /**
     * @param BeforeActionEvent $event
     */
    public function execute(BeforeActionEvent $event)
    {
        $model = RateRequest::find()->where([
                'action_id' => $event->sender->id,
                'ip' => \Yii::$app->request->getUserIP(),
                'user_agent' => \Yii::$app->request->getUserAgent()
            ])->one() ?: new RateRequest();

        if ($model->isNewRecord) {
            $model->action_id = $event->sender->id;
            $model->ip = \Yii::$app->request->getUserIP();
            $model->user_agent = \Yii::$app->request->getUserAgent();
            $model->created_at = $model->created_at ?: time();
        }
        $model->count = $model->isNewRecord ? 1 : $model->count + 1;
        $model->last_request = time();

        $model->save();

        $event->sender->setUpdateObserver($model);
    }

}