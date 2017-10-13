<?php
namespace rest\modules\chat\controllers\actions;

use Yii;
use yii\rest\Action;
use rest\modules\chat\models\ChannelMessage;

/**
 * Class MessageAction
 */
class MessageAction extends Action
{
    /**
     * @param string $id channel id
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $user = Yii::$app->user->identity;
        $message = new ChannelMessage();
        $message->setAttributes(Yii::$app->request->getBodyParams());
        $message->user_id = $user->id;
        $message->channel_id = $model->id;
        $message->save();
        return $message;
    }
}
