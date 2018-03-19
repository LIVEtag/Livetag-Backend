<?php
namespace rest\modules\chat\controllers\actions;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\rest\Action;

/**
 * Class GetMessagesAction
 */
class GetMessagesAction extends Action
{
    /**
     * @param string $id channel id
     * @return ActiveDataProvider
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getMessages(),
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]]
        ]);
        return $dataProvider;
    }
}
