<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Shop;

use rest\common\models\Product\ProductSearch;
use rest\common\models\Shop\Shop;
use Yii;
use yii\rest\Action;
use yii\helpers\ArrayHelper;

/**
 * Class ProductsAction
 */
class ProductsAction extends Action
{

    public function run($slug)
    {
        /** @var Shop $shop */
        $shop = $this->findModel($slug);
        if ($this->checkAccess) {
            // phpcs:disable
            call_user_func($this->checkAccess, $this->id, $shop);
            // phpcs:enable
        }
        $params = ArrayHelper::merge(Yii::$app->request->queryParams, ['shopId' => $shop->id]);
        $searchModel = new ProductSearch();
        return $searchModel->search($params);
    }
}
