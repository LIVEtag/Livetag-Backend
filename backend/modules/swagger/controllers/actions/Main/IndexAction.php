<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\swagger\controllers\actions\Main;

use Yii;
use yii\base\Action;
use yii\web\Controller;

/**
 * Class IndexAction
 */
class IndexAction extends Action
{
    /**
     * IndexAction constructor.
     *
     * @param string $id
     * @param Controller $controller
     * @param array $config
     */
    public function __construct($id, Controller $controller, array $config = [])
    {
        parent::__construct($id, $controller, $config);
    }

    /**
     * Run action
     *
     * @return string
     */
    public function run()
    {
        $isSecure = Yii::$app->getRequest()->getIsSecureConnection();

        $basePath = $isSecure ? 'https://' : 'http://'. Yii::getAlias('@rest.domain');

        return $this->controller->render(
            'index',
            [
                'basePath' => $basePath,
            ]
        );
    }
}
