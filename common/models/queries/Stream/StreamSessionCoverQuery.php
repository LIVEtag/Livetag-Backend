<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Stream;

use common\models\Stream\StreamSessionCover;
use common\components\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Stream\StreamSessionCover]].
 *
 * @see StreamSessionCover
 */
class StreamSessionCoverQuery extends ActiveQuery
{

    /**
     * @param int|array $id
     * @return $this
     */
    public function byId($id): self
    {
        return $this->andWhere([$this->getFieldName('id') => $id]);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function byStreamSessionId(int $id): self
    {
        return $this->andWhere([$this->getFieldName('streamSessionId') => $id]);
    }
}
