<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Stream;

use common\models\Stream\StreamSession;
use common\components\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Stream\StreamSession]].
 *
 * @see StreamSession
 */
class StreamSessionQuery extends ActiveQuery
{

    /**
     * @param int $shopId
     * @return $this
     */
    public function byShopId(int $shopId): self
    {
        return $this->andWhere([$this->getFieldName('shopId') => $shopId]);
    }

    /**
     * Return session with active status, and not expired token
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['>', $this->getFieldName('expiredAt'), time()])
                ->andWhere([$this->getFieldName('status') => StreamSession::STATUS_ACTIVE]);
    }

    /**
     * Order by creation date. Latest first
     * @return $this
     */
    public function orderByLatest()
    {
        return $this->orderBy([$this->getFieldName('createdAt') => SORT_DESC]);
    }
}