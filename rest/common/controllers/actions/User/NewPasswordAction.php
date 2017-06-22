<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use rest\common\models\views\User\RecoveryPassword;
use rest\common\observers\UpdateObserver;
use rest\common\observers\ZeroingObserver;
use rest\common\services\User\RateRequestService;
use rest\components\api\actions\Action;
use rest\components\api\actions\events\BeforeActionEvent;
use yii\rest\Controller;
use yii\web\TooManyRequestsHttpException;

/**
 * Class NewPasswordAction
 */
class NewPasswordAction extends Action
{
    /**
     * Event name
     */
    const EVENT_BEFORE_RUN = 'EVENT_BEFORE_RUN';

    /**
     * NewPasswordAction constructor.
     * @param string $id
     * @param Controller $controller
     * @param array $config
     */
    public function __construct($id, Controller $controller, array $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->on(
            self::EVENT_BEFORE_RUN,
            [\Yii::createObject(ZeroingObserver::class, [RateRequestService::DENIED_TIME]), 'execute']
        );

        $this->on(self::EVENT_BEFORE_RUN, [\Yii::createObject(UpdateObserver::class), 'execute']);
    }

    /**
     * @return RecoveryPassword
     * @throws TooManyRequestsHttpException
     */
    public function run()
    {
        if (!\Yii::createObject(RateRequestService::class)->check()) {
            throw new TooManyRequestsHttpException('Access denied');
        }
        $params = \Yii::$app->request->getBodyParams();
        $user = User::findByPasswordResetToken($params['resetToken']);
        /** @var RecoveryPassword $recovery */
        $recovery = \Yii::createObject(RecoveryPassword::class, [$user]);
        $recovery->setAttributes($params);

        return $recovery->recovery();
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
