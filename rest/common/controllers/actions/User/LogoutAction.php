<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use rest\common\models\User;
use rest\components\api\actions\Action;
use Yii;

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
        $this->response->setStatusCode(204);
    }
}
