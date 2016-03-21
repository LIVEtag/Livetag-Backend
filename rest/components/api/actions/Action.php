<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api\actions;

use Yii;
use yii\base\Action as BaseAction;
use yii\rest\Controller;
use yii\web\Request;
use yii\web\Response;

/**
 * Class BaseAction
 */
abstract class Action extends BaseAction
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Constructor
     *
     * @param string $id
     * @param Controller $controller
     * @param array $config
     */
    public function __construct($id, Controller $controller, array $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->request = Yii::$app->getRequest();
        $this->response = Yii::$app->getResponse();
    }
}
