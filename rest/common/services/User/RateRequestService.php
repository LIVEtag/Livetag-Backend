<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\services\User;

use rest\common\models\RateRequest;

/**
 * Class RateRequestService
 */
class RateRequestService
{
    /**
     * Counter of the access per DENIED_TIME
     */
    const ACCESS_COUNT = 3;

    /**
     * The time after which set to zero counter requests
     */
    const DENIED_TIME = 3600;

    /**
     * Check access by count requests
     *
     * @param int $count
     * @param int $time
     * @return bool
     */
    public function check($count = RateRequestService::ACCESS_COUNT, $time = RateRequestService::DENIED_TIME)
    {
        $model = $this->search(
            \Yii::$app->controller->action->getUniqueId(),
            \Yii::$app->request->getUserIP(),
            \Yii::$app->request->getUserAgent()
        );
        return !($model->count > $count && ($model->last_request - $model->created_at) <= $time);
    }

    /**
     * Search RateRequest (model for count requests)
     *
     * @param string $action_id
     * @param string $ip
     * @param string $user_agent
     * @return array|RateRequest
     */
    public function search($action_id, $ip, $user_agent)
    {
        $attributes = [
            'action_id' => $action_id,
            'ip' => $ip,
            'user_agent' => $user_agent
        ];
        return RateRequest::find()->where($attributes)->one() ?: new RateRequest($attributes);
    }
}
