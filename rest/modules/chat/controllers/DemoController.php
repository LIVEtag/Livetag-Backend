<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);
namespace rest\modules\chat\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * DemoController
 */
class DemoController extends Controller
{

    /**
     * @return string
     * some basic things to work (this is only demo)
     */
    public function actionIndex($action)
    {
        if (!$action) {
            $action = 'index.html';
        }
        $file = Yii::getAlias('@chat/views/demo/' . $action);
        if (file_exists($file)) {
            $extentionTypes = [
                '.css' => 'text/css',
                '.js' => 'application/javascript'
            ];
            $extension = strrchr($file, '.');
            \Yii::$app->response->format = Response::FORMAT_RAW;
            $headers = Yii::$app->response->headers;
            if (isset($extentionTypes[$extension])) {
                $headers->add('Content-Type', $extentionTypes[$extension]);
            } else {
                $headers->add('Content-Type', 'text/html');
            }



            return $this->getView()->renderFile($file);
        }
        throw new \yii\web\NotFoundHttpException();
    }
}
