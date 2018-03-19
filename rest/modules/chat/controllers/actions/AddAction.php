<?php
namespace rest\modules\chat\controllers\actions;

use rest\modules\chat\models\User;
use Yii;
use yii\db\ActiveRecord;
use yii\rest\Action;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class AddAction
 */
class AddAction extends Action
{
    /**
     * add user to channel
     *
     * @param int $id channel id
     * @param int $userId
     * @return ActiveRecord
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function run($id, $userId)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $user = User::findOne($userId);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', "User not found: $id"));
        } else if ($user->id == Yii::$app->getModule('chat')->user->id) {
            throw new UnprocessableEntityHttpException(Yii::t('app', "You can't add self"));
        }

        if (!$model->joinUserToChannel($user)) {
            return $model;
        }

        Yii::$app->getResponse()->setStatusCode(204);

        return $model;
    }
}
