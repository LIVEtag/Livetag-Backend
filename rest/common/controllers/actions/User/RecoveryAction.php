<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use common\models\User;
use rest\common\models\views\User\ChangePassword;
use rest\common\models\views\User\RecoveryPassword;
use rest\common\models\views\User\SendRecoveryEmailForm;
use rest\components\api\actions\Action;
use yii\web\NotFoundHttpException;

/**
 * Class RecoveryAction
 */
class RecoveryAction extends Action
{
    public function run()
    {
        $model = new SendRecoveryEmailForm();
        $model->setAttributes($this->request->post());
        if (!$model->validate()) {
            return $model;
        }

        $model->generateAndSendEmail();

        \Yii::$app->getResponse()->setStatusCode(204);
    }
}
