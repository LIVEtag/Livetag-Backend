<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream\Archive;

use common\models\Stream\StreamSession;
use rest\common\models\Stream\Archive\ProductSearch;
use yii\rest\Action;
use yii\web\NotFoundHttpException;

class ProductsAction extends Action
{
    /**
     * @param int $id
     * @return ProductSearch|\yii\data\ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function run(int $id)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $this->findModel($id);
        if (!$streamSession->isArchived()) {
            throw new NotFoundHttpException('Archived Stream Session was not found.');
        }

        $searchModel = new ProductSearch($streamSession);
        return $searchModel->search();
    }
}
