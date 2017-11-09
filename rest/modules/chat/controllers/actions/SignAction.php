<?php
namespace rest\modules\chat\controllers\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * Class SignAction
 * @see https://fzambia.gitbooks.io/centrifugal/content/server/connection_check.html
 */
class SignAction extends Action
{

    public function run()
    {
        $user = Yii::$app->getModule('chat')->user->identity;
        return Yii::$app->getModule('chat')->centrifugo
                ->setUser($user)
                ->generateUserToken();
    }
}
