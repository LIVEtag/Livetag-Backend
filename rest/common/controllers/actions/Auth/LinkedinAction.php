<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\Auth;

use common\models\User\SocialProfile;
use rest\common\models\views\User\SocialForm;
use yii\web\ServerErrorHttpException;

/**
 * Class LinkedinAction
 */
class LinkedinAction extends AbstractAuthAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $client = $this->getClient('linkedin');
        $attributes = $this->authOAuth2($client);

        $socialForm = new SocialForm();
        $socialForm->email = $attributes['email'];
        $socialForm->socialType = SocialProfile::TYPE_LINKEDIN;
        $socialForm->socialId = $attributes['id'];
        $socialForm->userIp = $this->request->getUserIP();
        $socialForm->username = substr($socialForm->email, 0, strpos($socialForm->email, '@'));
        $socialForm->userAgent = $this->request->getUserAgent();

        $user = $socialForm->login();

        if ($user === null && !$socialForm->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create new user.');
        }

        if ($socialForm->hasErrors()) {
            return $socialForm;
        }

        $this->response->setStatusCode(201);

        return $user->accessToken;
    }
}
