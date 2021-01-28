<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use common\models\forms\User\ChangePasswordForm;
use Yii;
use yii\base\Action;

/**
 * Class ChangePasswordAction
 */
class ChangePasswordAction extends Action
{
    /**
     * Get current User;
     * Load post params to ChangePassword Model
     */
    public function run()
    {
        $model = new ChangePasswordForm();
        $user = Yii::$app->user->identity;
        $model->setAttributes(Yii::$app->request->post());
        if (!$model->changePassword($user)) {
            return $model;
        }
        Yii::$app->response->setStatusCode(204);
    }
}
