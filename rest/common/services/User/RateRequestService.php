<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
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
     * Check access by count requests
     *
     * @param int $count
     * @return bool
     */
    public function check($count = RateRequestService::ACCESS_COUNT)
    {
        $model = $this->search(
            \Yii::$app->controller->action->getUniqueId(),
            \Yii::$app->request->getUserIP(),
            \Yii::$app->request->getUserAgent()
        );
        return $model->count > $count;
    }

    /**
     * Search RateRequest (model for count requests)
     *
     * @param string $actionId
     * @param string $ip
     * @param string $userAgent
     * @return array|RateRequest
     */
    public function search($actionId, $ip, $userAgent)
    {
        $attributes = [
            'actionId' => $actionId,
            'ip' => $ip,
            'userAgent' => $userAgent
        ];
        return RateRequest::find()->where($attributes)->one() ?: new RateRequest($attributes);
    }
}
