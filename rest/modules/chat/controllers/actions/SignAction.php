<?php
namespace rest\modules\chat\controllers\actions;

use Yii;
use yii\base\Action;
use rest\modules\chat\models\Channel;
use yii\web\Response;

/**
 * Class SignAction
 */
class SignAction extends Action
{

    public function run()
    {
        $user = Yii::$app->user->identity;
        return Yii::$app->getModule('chat')->centrifugo
                ->setUser($user)
                ->generateUserToken();
    }
}
