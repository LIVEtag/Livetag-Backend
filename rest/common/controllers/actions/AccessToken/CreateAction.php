<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\AccessToken;

use rest\common\models\views\AccessToken\CreateToken;
use rest\components\api\actions\Action;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateAction
 */
class CreateAction extends Action
{
    /**
     * Create access token
     */
    public function run()
    {
        $accessTokenCreate = new CreateToken();
        $accessTokenCreate->load($this->request->getBodyParams(), '');

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
