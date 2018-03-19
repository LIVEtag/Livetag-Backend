<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use rest\components\api\actions\Action;

/**
 * Class CurrentAction
 */
class CurrentAction extends Action
{
    /**
     * @return null|User
     */
    public function run()
    {
        return User::findOne(\Yii::$app->user->id);
    }
}
