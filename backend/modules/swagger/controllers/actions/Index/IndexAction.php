<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\swagger\controllers\actions\Index;

use Yii;
use yii\base\Action;

/**
 * Class IndexAction
 */
class IndexAction extends Action
{
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
