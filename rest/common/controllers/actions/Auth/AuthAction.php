<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Auth;

use rest\common\models\views\Auth\AuthModel;
use rest\common\models\views\Auth\TwitterAuthModel;
use rest\common\services\Auth\AuthClientService;
use rest\components\api\actions\Action;
use yii\web\ServerErrorHttpException;

class AuthAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($type)
    {
        $authModel = $this->createAuthModel($type);
        $authModel->setAttributes($this->request->getBodyParams());

        if (!$authModel->validate()) {
            return $authModel;
        }

        $socialForm = $authModel->createSocialForm();
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

    /**
     * @param string $type
     * @return AuthModel
     */
    private function createAuthModel(string $type): AuthModel
    {
        $authService = new AuthClientService();
        //for twitter we use another model because it doesn't support oauth2
        if ($type == AuthModel::TYPE_TWITTER) {
            $authModel = new TwitterAuthModel($authService);
        } else {
            $authModel = new AuthModel($authService, $type);
        }

        return $authModel;
    }
}
