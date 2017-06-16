<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use rest\common\models\views\User\RecoveryPassword;
use rest\components\api\actions\Action;

/**
 * Class SetNewPasswordAction
 */
class NewPasswordAction extends Action
{
    /**
     * @return RecoveryPassword
     */
    public function run()
    {
        $recovery = new RecoveryPassword();
        $recovery->setAttributes(\Yii::$app->request->post());

        return $recovery->recovery();
    }
}
