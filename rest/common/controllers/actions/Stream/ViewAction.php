<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use rest\components\api\actions\Action;
use yii\web\NotFoundHttpException;

class ViewAction extends Action
{

    /**
     * @param int $id Shop id
     */
    public function run(int $id)
    {
        $streamSession = StreamSession::getCurrent($id);
        if (!$streamSession) {
            throw new NotFoundHttpException('No Active Stream Session');
        }
        return $streamSession;
    }
}
