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
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class StopAction extends Action
{

    public function run()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $shop = $user->shop;
        if (!$shop) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this action'));
        }
        if (!StreamSession::stopTranslation($shop)) {
            throw new BadRequestHttpException(Yii::t('app', 'Failed to Stop session for unknown reason'));
        }
        $this->response->setStatusCode(204);
    }
}
