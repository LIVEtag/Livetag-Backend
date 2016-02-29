<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\views\User\SignupUser;
use rest\components\api\actions\Action;
use Yii;
use yii\helpers\Url;
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
        /* @var $signupUser SignupUser */
        $signupUser = new $this->modelClass();
        $signupUser->load($this->request->getBodyParams(), '');

        $user = $signupUser->signup();

        if ($user === null && !$signupUser->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create new user.');
        }

        if ($signupUser->hasErrors()) {
            return $signupUser;
        }

        $this->response->setStatusCode(201);
        $this->response->getHeaders()
            ->set('Location', Url::toRoute(['view', 'id' => $user->getId()], true));

        return $user;
    }
}
