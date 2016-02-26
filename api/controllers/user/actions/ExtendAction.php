<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace api\controllers\user\actions;

use Yii;
use yii\helpers\Url;

/**
 * Display user token for current identity
 */
class ExtendAction extends \yii\rest\Action {
    /**
     * @var string the name of the view action. This property is need to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        /* @var $model \common\models\UserToken */
        $model = $this->findModel();

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->extend();
        
        if (!$model->extend()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        
        $id = implode(',', array_values($model->getPrimaryKey(true)));
        Yii::$app->getResponse()->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));

        return $model;
    }
    
    /**
     * @inheritdoc
     */
    public function findModel() {
        return Yii::$app->user->identity->userToken;
    }
}