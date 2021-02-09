<?php
namespace rest\common\controllers\actions\Centrifugo;

use Yii;
use yii\base\Action;

/**
 * Class SignAction
 */
class SignAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $token = Yii::$app->centrifugo->client->generateConnectionToken(Yii::$app->user->id);
        return ['token' => $token];
    }
}
