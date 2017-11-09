<?php
namespace rest\modules\chat\controllers\actions;

use Yii;
use yii\rest\Action;
use yii\data\ActiveDataProvider;

/**
 * Class GetUsersAction
 */
class GetUsersAction extends Action
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
        
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getUsers(),
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]]
        ]);
        return $dataProvider;
    }

     
}
