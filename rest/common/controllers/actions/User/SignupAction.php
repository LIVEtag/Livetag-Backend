<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use rest\common\models\views\User\SignupUser;
use rest\components\api\actions\Action;

/**
 * @deprecated
 * @todo remove
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

        if (!$signupUser->validate()) {
            return $signupUser;
        }

        return $signupUser->signup();
    }
}
