<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\Pages;

use rest\common\models\StaticPage;
use rest\components\api\actions\Action;

/**
 * Class ViewAction
 */
class ViewAction extends Action
{
    /**
     * @return null|StaticPage
     */
    public function run($slug)
    {
        return StaticPage::find()->where(['slug' => $slug])->one();
    }
}
