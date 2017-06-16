<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use rest\common\services\User\RateRequestService;
use rest\components\api\actions\Action;

/**
 * Class RecoveryAction
 */
class RecoveryAction extends Action
{

    /**
     * @return bool|User
     */
    public function run()
    {
        $user = User::findByEmail(\Yii::$app->request->post('email'));
        $rateRequest = RateRequestService::rateRequest($user);
        if (!$rateRequest) {
            $user->addError('password_reset_token', 'Access denied');
            return $user;
        }
        $user->generatePasswordResetToken();
        if (!$user->save()) {
            return false;
        }
        \Yii::$app->mailer->compose('recovery-password', [
            'user' => $user,
        ])->send();
        return $user;
    }
}
