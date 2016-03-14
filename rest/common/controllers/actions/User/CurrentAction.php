<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use rest\components\api\actions\Action;
use Yii;

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
        return User::find()
            ->andWhere('status = :status', [':status' => User::STATUS_ACTIVE])
            ->andWhere('id = :id', [':id' => Yii::$app->user->identity->getId()])
            ->one();
    }
}
