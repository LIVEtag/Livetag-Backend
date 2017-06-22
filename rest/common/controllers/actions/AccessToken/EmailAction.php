<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\AccessToken;

use rest\common\controllers\actions\Auth\AbstractAuthAction;
use rest\common\models\views\AccessToken\CreateToken;
use yii\web\ServerErrorHttpException;

/**
 * Class EmailAction
 */
class EmailAction extends AbstractAuthAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $accessTokenCreate = new CreateToken();
        $accessTokenCreate->setAttributes($this->request->getBodyParams());
        $accessTokenCreate->username = $this->request->getBodyParam('email');
        $accessTokenCreate->isRememberMe = filter_var(
            $this->request->getBodyParam('is_remember_me'), FILTER_VALIDATE_BOOLEAN
        ) ?
            CreateToken::YES_VALUE :
            CreateToken::NO_VALUE;

        $accessTokenCreate->userAgent = $this->request->getUserAgent();
        $accessTokenCreate->userIp = $this->request->getUserIP();

        $accessToken = $accessTokenCreate->create();

        if ($accessToken === null && !$accessTokenCreate->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create access token.');
        }

        if ($accessTokenCreate->hasErrors()) {
            return $accessTokenCreate;
        }

        $this->response->setStatusCode(201);

        return $accessToken;
    }
}
