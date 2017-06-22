<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\observers;

use rest\common\services\User\RateRequestService;
use rest\components\api\actions\events\BeforeActionEvent;

/**
 * Class UpdateObserver
 */
class UpdateObserver
{
    /**
     * @var RateRequestService
     */
    private $rateRequestService;

    /**
     * UpdateObserver constructor.
     * @param RateRequestService $rateRequestService
     */
    public function __construct(RateRequestService $rateRequestService)
    {
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

        if ($model->isNewRecord) {
            $model->action_id = $event->sender->getUniqueId();
            $model->ip = \Yii::$app->request->getUserIP();
            $model->user_agent = \Yii::$app->request->getUserAgent();
            $model->created_at = $model->created_at ?: time();
        }
        $model->count = $model->isNewRecord ? 1 : $model->count + 1;
        $model->last_request = time();

        $model->save();
    }

}