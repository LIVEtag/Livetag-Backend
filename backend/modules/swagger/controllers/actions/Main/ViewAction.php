<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\swagger\controllers\actions\Main;

use yii\base\Action;

/**
 * Class ViewAction
 */
class ViewAction extends Action
{
    /**
     * @return string
     */
    public function run()
    {
        return $this->controller->render('view');
    }
}
