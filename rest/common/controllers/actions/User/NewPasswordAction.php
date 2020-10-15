<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use Yii;
use yii\web\NotFoundHttpException;
use common\models\User;
use rest\common\models\views\User\RecoveryPassword;
use rest\components\api\actions\Action;

/**
 * Class NewPasswordAction
 */
class NewPasswordAction extends Action
{
    /**
     * @return RecoveryPassword|void
     */
    public function run()
    {
        $params = \Yii::$app->request->getBodyParams();
        $user = User::findByPasswordResetToken($params['resetToken']);
        if ($user === null) {
            throw new NotFoundHttpException('User has been not found.');
        }

        /** @var RecoveryPassword $recovery */
        $recovery = \Yii::createObject(RecoveryPassword::class);
        $recovery->setAttributes($params);
        $recovery->recovery($user);
        if ($recovery->hasErrors()) {
            return $recovery;
        }

        Yii::$app->response->setStatusCode(204);
    }
}
