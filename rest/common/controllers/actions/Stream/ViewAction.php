<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Shop\Shop;
use common\models\Stream\StreamSession;
use rest\components\api\actions\Action;
use yii\web\NotFoundHttpException;

class ViewAction extends Action
{

    /**
     * @param int $slug Shop Uri
     */
    public function run($slug)
    {
        $shop = Shop::find()->byUri($slug)->one();
        if (!$shop) {
            throw new NotFoundHttpException('Shop Not Found');
        }
        $streamSession = StreamSession::getCurrent($shop->id);
        if (!$streamSession) {
            throw new NotFoundHttpException('No Active Stream Session');
        }
        return $streamSession;
    }
}
