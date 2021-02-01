<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use rest\common\models\Stream\StreamSessionProductSearch;
use Yii;
use yii\rest\Action;

class ProductsAction extends Action
{

    /**
     * @param int $id
     */
    public function run(int $id)
    {
        /** @var StreamSession $streamSession */
        $streamSession = $this->findModel($id);
        if ($this->checkAccess) {
            // phpcs:disable
            call_user_func($this->checkAccess, $this->id, $streamSession);
            // phpcs:enable
        }
        $searchModel = new StreamSessionProductSearch($streamSession);
        return $searchModel->search(Yii::$app->request->queryParams);
    }
}
