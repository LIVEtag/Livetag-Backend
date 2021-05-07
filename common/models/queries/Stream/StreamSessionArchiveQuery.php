<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Stream;

use common\components\db\ActiveQuery;
use common\models\Stream\StreamSessionArchive;

class StreamSessionArchiveQuery extends ActiveQuery
{
    /**
     * Return ready archive
     *
     * @return $this
     */
    public function ready()
    {
        return $this->byStatus(StreamSessionArchive::STATUS_READY);
    }

    /**
     * @param int|array $id
     * @return $this
     */
    public function byId($id): self
    {
        return $this->andWhere([$this->getFieldName('id') => $id]);
    }

    /**
     * @param int|array $status
     * @return $this
     */
    public function byStatus($status): self
    {
        return $this->andWhere([$this->getFieldName('status') => $status]);
    }
}
