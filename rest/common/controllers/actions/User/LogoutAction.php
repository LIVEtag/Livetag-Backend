<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use Yii;
use yii\base\Action;

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
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $user->accessToken->invalidate();
        Yii::$app->response->setStatusCode(204);
    }
}
