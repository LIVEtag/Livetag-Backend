<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\swagger\controllers\actions\Index;

use yii\base\Action;
use yii\web\Response;

/**
 * Class JsonAction
 */
class JsonAction extends Action
{
    /**
     * Run action
     */
    public function run()
    {
        \Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        $content = file_get_contents(\Yii::getAlias('@swagger') . '/config/swagger.json');
        if (!$content) {
            return [];
        }

        return json_decode($content, true);
    }
}
