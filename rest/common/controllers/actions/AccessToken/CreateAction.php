<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\AccessToken;

use rest\common\models\views\AccessToken\CreateToken;
use rest\components\api\actions\Action;
use Yii;
use yii\helpers\Url;
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
        /* @var $accessTokenCreate CreateToken */
        $accessTokenCreate = new $this->modelClass();
        $accessTokenCreate->load($this->request->getBodyParams(), '');

        $accessTokenCreate->userAgent = Yii::$app->getRequest()->getUserAgent();
        $accessTokenCreate->userIp = Yii::$app->getRequest()->getUserIP();

        $accessToken = $accessTokenCreate->create();

        if ($accessToken === null && !$accessTokenCreate->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create access token.');
        }

        if ($accessTokenCreate->hasErrors()) {
            return $accessTokenCreate;
        }

        $this->response->setStatusCode(201);
        $this->response->getHeaders()
            ->set('Location', Url::toRoute(['view', 'id' => $accessToken->getId()], true));

        return $accessToken;
    }
}
