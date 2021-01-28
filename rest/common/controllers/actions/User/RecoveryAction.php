<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use common\models\forms\User\SendRecoveryEmailForm;
use Yii;
use yii\base\Action;

/**
 * Class RecoveryAction
 */
class RecoveryAction extends Action
{
    public function run()
    {
        $model = new SendRecoveryEmailForm();
        $model->setAttributes(Yii::$app->request->post());
        if (!$model->validate()) {
            return $model;
        }

        $model->generateAndSendEmail();

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
