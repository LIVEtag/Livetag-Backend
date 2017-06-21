<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\observers;

use rest\common\models\RateRequest;
use rest\components\api\actions\events\BeforeActionEvent;

class ZeroingObserver
{
    private $time;

    /**
     * ZeroingObserver constructor.
     * @param $time
     */
    public function __construct($time)
    {
        $this->time = $time;
    }

    /**
     * @param BeforeActionEvent $event
     */
    public function execute(BeforeActionEvent $event)
    {
        /** @var RateRequest $model */
        $model = RateRequest::find()->where([
            'action_id' => $event->sender->id,
            'ip' => \Yii::$app->request->getUserIp(),
            'user_agent' => \Yii::$app->request->getUserAgent()
        ])->one();
        if (!empty($model) && ($model->last_request - $model->created_at) >= $this->time) {
            $model->count = 0;
            $model->created_at = time();
            $model->last_request = time();
            $model->save();
        }
    }

    /**
     * @param int $time
     * @return ZeroingObserver
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }
}