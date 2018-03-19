<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\views\User\SignupUser;
use rest\components\api\actions\Action;
use yii\web\ServerErrorHttpException;

/**
 * Class SignupAction
 */
class SignupAction extends Action
{
    /**
     * Signup new user
     */
    public function run()
    {
        $signupUser = new SignupUser();
        $signupUser->setAttributes($this->request->getBodyParams());

        $signupUser->userAgent = $this->request->getUserAgent();
        $signupUser->userIp = $this->request->getUserIP();

        $user = $signupUser->signup();

        if ($user === null && !$signupUser->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create new user.');
        }

        if ($signupUser->hasErrors()) {
            return $signupUser;
        }

        $this->response->setStatusCode(201);

        return $user;
    }
}
