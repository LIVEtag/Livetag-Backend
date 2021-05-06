<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Stream;

use common\components\db\ActiveQuery;

class StreamSessionLikeQuery extends ActiveQuery
{
    /**
     * @param int $streamSessionId
     * @return $this
     */
    public function byStreamSessionId(int $streamSessionId): self
    {
        return $this->andWhere([$this->getFieldName('streamSessionId') => $streamSessionId]);
    }

    /**
     * @param int $from
     * @param int $to
     * @return StreamSessionLikeQuery
     */
    public function betweenTimestamps(int $from, int $to)
    {
        return $this->andWhere(['between', $this->getFieldName('createdAt'), $from, $to]);
    }

    /**
     * @param int $from
     * @return StreamSessionLikeQuery
     */
    public function afterTimestamp(int $from)
    {
        return $this->andWhere(['>', $this->getFieldName('createdAt'), $from]);
    }

    /**
     * @param int $to
     * @return StreamSessionLikeQuery
     */
    public function beforeTimestamp(int $to)
    {
        return $this->andWhere(['<=', $this->getFieldName('createdAt'), $to]);
    }
}
