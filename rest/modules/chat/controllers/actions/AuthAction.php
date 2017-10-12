<?php
namespace rest\modules\chat\controllers\actions;

use Yii;
use yii\base\Action;
use rest\modules\chat\models\Channel;
use yii\web\Response;

/**
 * Class AuthAction
 *
 * @see https://fzambia.gitbooks.io/centrifugal/content/mixed/private_channels.html
 * @see php example here https://gist.github.com/Malezha/a9bdfbddee15bfd624d4
 */
class AuthAction extends Action
{

    public function run()
    {
        $user = Yii::$app->user->identity;

        $request = Yii::$app->request;
        $client = $request->post('client');
        $channels = $request->post('channels');
        $channels = is_array($channels) ? $channels : [$channels];

        $response = [];

        foreach ($channels as $channel) {
            $channelModel = Channel::find()->byUrl($channel)->one();
            if (!$channelModel) {
                $response[$channel] = [
                    'status' => 404,
                ];
            } elseif ($channelModel->canAccess($user)) {
                Yii::$app->getModule('chat')->centrifugo->setUser($user);
                $response[$channel] = Yii::$app->getModule('chat')->centrifugo->generateChannelSignResponce($channel, $client);
            } else {
                $response[$channel] = [
                    'status' => 403,
                ];
            }
        }

        return $response;
    }
}
