<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace api\controllers\user\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Display user token for current identity
 */
class ViewAction extends \yii\rest\ViewAction {
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        $model = parent::findModel($id);
        
        // Check for current user
        if ($model->user->primaryKey != Yii::$app->user->identity->primaryKey) {
            throw new ServerErrorHttpException('View this object is forbidden.');
        }

        return $model;
    }
}