<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\observers;

use rest\common\services\User\RateRequestService;
use rest\components\api\actions\events\BeforeActionEvent;

/**
 * Class ZeroingObserver
 */
class ZeroingObserver
{
    /**
     * @var int
     */
    private $time;
    /**
     * @var RateRequestService
     */
    private $rateRequestService;

    /**
     * ZeroingObserver constructor.
     * @param $time
     * @param RateRequestService $rateRequestService
     */
    public function __construct($time, RateRequestService $rateRequestService)
    {
        $this->time = $time;
        $this->rateRequestService = $rateRequestService;
    }

    /**
     * @param BeforeActionEvent $event
     */
    public function execute(BeforeActionEvent $event)
    {
        $model = $this->rateRequestService->search($event);

        if (!$model->id && ($model->last_request - $model->created_at) >= $this->time) {
            $model->count = 0;
            $model->created_at = time();
            $model->last_request = time();
            $model->save();
        }
    }
}