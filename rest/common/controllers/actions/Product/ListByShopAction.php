<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Product;

use rest\common\models\Product\ProductSearch;
use rest\components\api\actions\Action;
use Yii;

/**
 * Class ListByShopAction
 */
class ListByShopAction extends Action
{
    public function run()
    {
        $params = Yii::$app->request->queryParams;
        $searchModel = new ProductSearch();
        return $searchModel->search($params);
    }
}
