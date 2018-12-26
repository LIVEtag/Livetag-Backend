<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use League\Container\Exception\NotFoundException;
use rest\common\models\User;
use rest\common\models\views\User\RecoveryPassword;
use rest\common\observers\UpdateObserver;
use rest\common\observers\ZeroingObserver;
use rest\common\services\User\RateRequestService;
use rest\components\api\actions\Action;
use rest\components\api\actions\events\BeforeActionEvent;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\TooManyRequestsHttpException;

/**
 * Class RecoveryAction
 */
class RecoveryAction extends Action
{
    /**
     * Event name
     */
    const EVENT_BEFORE_RUN = 'EVENT_BEFORE_RUN';

    /**
     * RecoveryAction constructor
     *
     * @param string $id
     * @param Controller $controller
     * @param array $config
     */
    public function __construct($id, Controller $controller, array $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->on(
            self::EVENT_BEFORE_RUN,
            [\Yii::createObject(ZeroingObserver::class, [ZeroingObserver::DENIED_TIME]), 'execute']
        );
        $this->on(self::EVENT_BEFORE_RUN, [\Yii::createObject(UpdateObserver::class), 'execute']);
    }

    /**
     * @return User
     * @throws TooManyRequestsHttpException
     */
    public function run()
    {
        if (\Yii::createObject(RateRequestService::class)->check()) {
            throw new TooManyRequestsHttpException('Access denied.');
        }

        $user = User::findByEmail(\Yii::$app->request->getBodyParam('email'));

        if ($user === null) {
            throw new NotFoundHttpException('User has been not found.');
        }

        \Yii::createObject(RecoveryPassword::class)->generateAndSendEmail($user);

        \Yii::$app->getResponse()->setStatusCode(204);
        return;
    }

    /**
     * @return bool
     */
    protected function beforeRun()
    {
        $this->trigger(self::EVENT_BEFORE_RUN, new BeforeActionEvent());
        return true;
    }
}
