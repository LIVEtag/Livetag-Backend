<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use rest\components\api\actions\Action;

/**
 * Class LogoutAction
 */
class LogoutAction extends Action
{
    /**
     * set current user access token inactive
     */
    public function run(): void
    {
        \Yii::$app->user->accessToken->invalidate();
        \Yii::$app->getResponse()->setStatusCode(204);
    }
}
