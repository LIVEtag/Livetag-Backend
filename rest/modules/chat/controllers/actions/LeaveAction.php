<?php
namespace rest\modules\chat\controllers\actions;

use Yii;
use yii\rest\Action;

/**
 * Class JoinAction
 */
class LeaveAction extends Action
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
        if (!$model->leaveUserFromChannel($user)) {
            return $model;
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
