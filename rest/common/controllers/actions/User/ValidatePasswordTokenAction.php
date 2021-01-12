<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use common\models\User;
use rest\components\api\actions\Action;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class ValidatePasswordTokenAction
 */
class ValidatePasswordTokenAction extends Action
{
    /**
     * @param $token
     *
     * @throws NotFoundHttpException
     * @noinspection MissingParameterTypeDeclarationInspection
     * @noinspection MethodShouldBeFinalInspection
     */
    public function run($token): void
    {
        $user = User::findByPasswordResetToken($token);
        
        if ($user === null) {
            throw new NotFoundHttpException('Invalid token.');
        }
        
        Yii::$app->getResponse()->setStatusCode(204);
    }
}
