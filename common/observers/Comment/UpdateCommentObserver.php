<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\Comment;

use common\components\centrifugo\Message;
use common\models\Comment\Comment;
use RuntimeException;
use yii\base\Event;

class UpdateCommentObserver
{

    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var Comment $comment */
        $comment = $event->sender;
        if (!($comment instanceof Comment)) {
            throw new RuntimeException('Not Comment instance');
        }

        $actionType = Message::ACTION_COMMENT_UPDATE;
        if (isset($event->changedAttributes['status']) && $comment->isDeleted()) {
            $actionType = Message::ACTION_COMMENT_DELETE;
        }

        $comment->notify($actionType);
    }
}
