<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\services\User;

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
     * @param $model
     * @param int $count
     * @param int $time
     * @return bool
     */
    public function check($model, $count = RateRequestService::ACCESS_COUNT, $time = RateRequestService::DENIED_TIME)
    {
        if ($model->count > $count && ($model->last_request - $model->created_at) <= $time) {
            return false;
        }

        return $model;
    }
}