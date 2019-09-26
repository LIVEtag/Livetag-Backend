<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use rest\common\models\views\User\ChangePassword;
use rest\components\api\actions\Action;

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
    public function run(): ChangePassword
    {
        $model = new ChangePassword();
        $user = \Yii::$app->user->identity;
        $model->setAttributes(\Yii::$app->request->post());
        $model->changePassword($user);
        return $model;
    }
}
