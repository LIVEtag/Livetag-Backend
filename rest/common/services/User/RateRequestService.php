<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\services\User;

use rest\common\models\RateRequest;
use yii\base\Action;
use yii\base\Event;

/**
 * Class RateRequestService
 */
class RateRequestService
{
    /**
     * Counter of the access per DENIED_TIME
     */
    const ACCESS_COUNT = 3000;

    /**
     * The time after which set to zero counter requests
     */
    const DENIED_TIME = 3600;

    /**
     * @param int $count
     * @param int $time
     * @return bool
     */
    public function check($count = RateRequestService::ACCESS_COUNT, $time = RateRequestService::DENIED_TIME)
    {
        $model = $this->search();
        if ($model->count > $count && ($model->last_request - $model->created_at) <= $time) {
            return false;
        }

        return true;
    }

    /**
     * @param Event $event
     * @return array|RateRequest
     */
    public function search(Event $event = null)
    {
        /** @var Action $sender */
        $sender = $event ? $event->sender : null;

        return RateRequest::find()->where([
            'action_id' => $sender ? $sender->getUniqueId() : \Yii::$app->controller->action->getUniqueId(),
            'ip' => $sender ? $sender->request->getUserIp() : \Yii::$app->getRequest()->getUserIP(),
            'user_agent' => $sender ? $sender->request->getUserAgent() : \Yii::$app->getRequest()->getUserAgent()
        ])->one() ?: new RateRequest();
    }
}