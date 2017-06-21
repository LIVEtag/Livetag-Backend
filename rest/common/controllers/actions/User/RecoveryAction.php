<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\interfaces\RecoveryPasswordInterface;
use rest\common\models\User;
use rest\common\observers\UpdateObserver;
use rest\common\observers\ZeroingObserver;
use rest\common\services\User\RateRequestService;
use rest\components\api\actions\Action;
use rest\components\api\actions\events\BeforeActionEvent;
use yii\rest\Controller;
use yii\web\TooManyRequestsHttpException;

/**
 * Class RecoveryAction
 */
class RecoveryAction extends Action implements RecoveryPasswordInterface
{
    const EVENT_BEFORE_RUN = 'EVENT_BEFORE_RUN';

    /**
     * @var UpdateObserver
     */
    private $updateObserver;

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
     * @return bool|User
     * @throws TooManyRequestsHttpException
     */
    public function run()
    {
        if (!\Yii::createObject(RateRequestService::class)->check($this->updateObserver)) {
            throw new TooManyRequestsHttpException('Access denied');
        }
        $user = User::findByEmail(\Yii::$app->request->getBodyParam('email'));
        if ($user) {
            $user->generatePasswordResetToken();
            if ($user->save()) {
                \Yii::$app->mailer->compose('recovery-password', [
                    'user' => $user,
                ])->send();
            }
        } else {
            $user = new User;
            $user->addError('username/email', 'Username/Email not found');
        }

        return $user;
    }

    protected function beforeRun()
    {
        $this->trigger(self::EVENT_BEFORE_RUN, new BeforeActionEvent());
        return true;
    }

    /**
     * @param UpdateObserver $updateObserver
     * @return RecoveryAction
     */
    public function setUpdateObserver($updateObserver)
    {
        $this->updateObserver = $updateObserver;
        return $this;
    }
}
