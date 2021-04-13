<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use rest\common\models\Product\PresentedProductSearch;
use yii\rest\Action;
use yii\web\NotFoundHttpException;

class PresentedProductsAction extends Action
{
    /**
     * @param int $id
     * @return PresentedProductSearch|\yii\data\ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function run(int $id)
    {
        /** @var StreamSession $streamSession */
        $streamSession = StreamSession::find()->byId($id)->archived()->published()->one();
        if (!$streamSession) {
            throw new NotFoundHttpException('Stream Session was not found.');
        }

        $searchModel = new PresentedProductSearch($streamSession);
        return $searchModel->search();
    }
}
