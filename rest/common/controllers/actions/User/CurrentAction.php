<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use common\models\User;
use Yii;
use yii\base\Action;

/**
 * Class CurrentAction
 */
class CurrentAction extends Action
{
    /**
     * @return User
     */
    public function run(): User
    {
        return Yii::$app->user->identity;
    }
}
