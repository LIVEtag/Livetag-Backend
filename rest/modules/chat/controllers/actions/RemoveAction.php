<?php
namespace rest\modules\chat\controllers\actions;

use Yii;
use yii\rest\Action;
use rest\common\models\User;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class RemoveAction
 */
class RemoveAction extends Action
{

    /**
     * remove user from channel
     *
     * @param int $id channel id
     * @param int $userId
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
        } elseif ($user->id == Yii::$app->user->id) {
            throw new UnprocessableEntityHttpException(Yii::t('app', "You can't remove self"));
        } elseif ($model->canManage($user)) {
            throw new UnprocessableEntityHttpException(Yii::t('app', "You can't remove other channel admin"));
        }

        if (!$model->leaveUserFromChannel($user)) {
            return $model;
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
