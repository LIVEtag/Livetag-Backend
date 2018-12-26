<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use Yii;
use rest\components\api\actions\Action;
use rest\common\models\AccessToken;

/**
 * Class LogoutAction
 */
class LogoutAction extends Action
{

    /**
     * set current user access token inactive
     */
    public function run()
    {
        $accessToken = AccessToken::find()
            ->findCurrentToken($this->request->getUserAgent(), $this->request->getUserIP())
            ->andWhere('userId = :userId', [':userId' => Yii::$app->user->id])
            ->one();
        if ($accessToken) {
            $accessToken->expiredAt = time();
            $accessToken->save(false, ['expiredAt']);
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
