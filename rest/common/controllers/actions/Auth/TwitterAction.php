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
 * Class TwitterAction
 */
class TwitterAction extends AbstractAuthAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $client = $this->getClient('twitter');
        $attributes = $this->authOAuth1($client);
        $socialForm = new SocialForm();

        $socialForm->email = !empty($attributes['email'])
            ? $attributes['email']
            : $attributes['id_str'] . '@twitter.com';
        $socialForm->socialType = SocialProfile::TYPE_TWITTER;
        $socialForm->socialId = $attributes['id_str'];
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
