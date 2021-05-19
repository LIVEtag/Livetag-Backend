<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\models\queries\Comment;

use common\models\Comment\Comment;
use common\components\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Comment\Comment]].
 *
 * @see Comment
 */
class CommentQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->byStatus(Comment::STATUS_ACTIVE);
    }

    /**
     * @param int|array $status
     * @return $this
     */
    public function byStatus($status): self
    {
        return $this->andWhere([$this->getFieldName('status') => $status]);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function byStreamSessionId(int $id): self
    {
        return $this->andWhere([$this->getFieldName('streamSessionId') => $id]);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function byUserId(int $id): self
    {
        return $this->andWhere([$this->getFieldName('userId') => $id]);
    }
}
