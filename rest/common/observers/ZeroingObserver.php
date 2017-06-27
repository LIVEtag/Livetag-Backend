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
     * The time after which set to zero counter requests
     */
    const DENIED_TIME = 3600;

    /**
     * @var int
     */
    private $time;

    /**
     * @var RateRequestService
     */
    private $rateRequestService;

    /**
     * ZeroingObserver constructor
     *
     * @param int $time
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
        $model = $this->rateRequestService->search(
            $event->sender->getUniqueId(),
            $event->sender->request->getUserIp(),
            $event->sender->request->getUserAgent()
        );

        $time = time();
        if (($time - $model->last_request) > $this->time) {
            $model->count = 0;
            $model->last_request = $time;
            $model->save();
        }
    }
}
