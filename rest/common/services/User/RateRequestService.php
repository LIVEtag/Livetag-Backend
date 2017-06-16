<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\services\User;

use rest\common\models\RateRequest;
use rest\common\models\User;

/**
 * Class RateRequestService
 */
class RateRequestService
{
    const ACCESS_COUNT = 3;
    /**
     * The time after which set to zero counter requests
     */
    const DENIED_TIME = 3600;

    /**
     * @param User $user
     * @return bool|RateRequest
     */
    public static function rateRequest(User $user = null)
    {
        $action_type = \Yii::$app->controller->action->id;
        $user_id = $user ? $user->id : null;
        $ip = \Yii::$app->request->getUserIP();
        $user_agent = \Yii::$app->request->getUserAgent();

        $model = RateRequest::find()->where([
            'action_type' => $action_type,
            'user_id' => $user_id,
            'ip' => $ip,
            'user_agent' => $user_agent
        ])->one() ?: new RateRequest();

        if ($model->isNewRecord) {
            $model->action_type = $action_type;
            $model->user_id = $user_id;
            $model->ip = $ip;
            $model->user_agent = $user_agent;
            $model->created_at = $model->created_at ?: time();
        }
        $model->count = $model->isNewRecord ? 1 : $model->count + 1;
        $model->last_request = time();

        $model->save();
        if (!self::check($model->count, [$model->created_at, $model->last_request], $model)) {
            return false;
        }

        return $model;
    }

    /**
     * @param int $count
     * @param array $interval [from, to]
     * @param RateRequestService $model
     * @return bool|RateRequestService $model
     */
    public static function check($count, $interval = [from, to], $model = null)
    {
        if (!$model && $count > self::ACCESS_COUNT || (time() - $interval[0]) > self::DENIED_TIME) {
            return false;
        }
        if ($count > self::ACCESS_COUNT) {
            if ((time() - $interval[0]) > self::DENIED_TIME) {
                $model->delete();

                return $model;
            }
            return false;
        }

        return $model;
    }
}