<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use rest\common\models\User;
use rest\components\api\actions\Action;
use Yii;
use yii\web\ForbiddenHttpException;

class CreateAction extends Action
{

    public function run()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $shop = $user->shop;
        if (!$shop) {
            throw new ForbiddenHttpException('You are not allowed to create Stream Session.');
        }
        return StreamSession::startTranslation($shop);
    }
}
