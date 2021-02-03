<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\Stream;

use common\models\Stream\StreamSession;
use rest\common\models\Comment\CreateCommentForm;
use Yii;
use yii\rest\Action;

class CommentCreateAction extends Action
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
        $form = new CreateCommentForm($streamSession, Yii::$app->user->identity);
        $form->setAttributes(Yii::$app->request->getBodyParams());
        $comment = $form->create();
        if (!$comment->hasErrors()) {
            Yii::$app->response->setStatusCode(201);
        }
        return $comment;
    }
}
