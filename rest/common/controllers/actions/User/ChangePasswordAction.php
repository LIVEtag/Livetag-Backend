<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use rest\common\models\views\User\ChangePassword;
use rest\components\api\actions\Action;
use Yii;

/**
 * Class ChangePasswordAction
 */
class ChangePasswordAction extends Action
{
    /**
     * Get current User;
     * Load post params to ChangePassword Model
     * @return ChangePassword
     */
    public function run()
    {
        $model = new ChangePassword();
        $user = User::findOne(Yii::$app->user->getId());
        $model->setAttributes(Yii::$app->request->post());
        $model->changePassword($user);
        return $model;
    }
}
