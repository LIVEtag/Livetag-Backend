<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Shop\Shop;
use rest\common\models\Stream\StreamSessionSearch;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;

class IndexAction extends Action
{
    /**
     * @param $slug
     * @return StreamSessionSearch|\yii\data\ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function run($slug)
    {
        $shop = Shop::find()->byUri($slug)->one();
        if (!$shop) {
            throw new NotFoundHttpException('Shop was not found.');
        }
        $searchModel = new StreamSessionSearch();

        return $searchModel->search(Yii::$app->request->queryParams, $shop->id);
    }
}
