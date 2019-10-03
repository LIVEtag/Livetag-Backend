<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use common\models\User;
use rest\common\models\views\User\RecoveryPassword;
use rest\components\api\actions\Action;
use yii\web\NotFoundHttpException;

/**
 * Class RecoveryAction
 */
class RecoveryAction extends Action
{
    /**
     * @return User
     */
    public function run()
    {
        $user = User::findByEmail(\Yii::$app->request->getBodyParam('email'));

        if ($user === null) {
            throw new NotFoundHttpException('User has been not found.');
        }

        \Yii::createObject(RecoveryPassword::class)->generateAndSendEmail($user);

        \Yii::$app->getResponse()->setStatusCode(204);
    }
}
