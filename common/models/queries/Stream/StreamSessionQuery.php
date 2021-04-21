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
     * @param int $id
     * @return $this
     */
    public function byId(int $id): self
    {
        return $this->andWhere([$this->getFieldName('id') => $id]);
    }

    /**
     * @param int $shopId
     * @return $this
     */
    public function byShopId(int $shopId): self
    {
        return $this->andWhere([$this->getFieldName('shopId') => $shopId]);
    }

    /**
     * @param string $externalId
     * @return $this
     */
    public function byExternalId(string $externalId): self
    {
        return $this->andWhere([$this->getFieldName('sessionId') => $externalId]);
    }

    /**
     * Return session with active status
     *
     * @return $this
     */
    public function active()
    {
        return $this->byStatus(StreamSession::STATUS_ACTIVE);
    }

    /**
     * Return session with archived status
     *
     * @return $this
     */
    public function archived()
    {
        return $this->byStatus(StreamSession::STATUS_ARCHIVED);
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
     * @return $this
     */
    public function commentsEnabled(): self
    {
        return $this->andWhere([$this->getFieldName('commentsEnabled') => true]);
    }

    /**
     * Order by creation date. Latest first
     * @return $this
     */
    public function orderByLatest()
    {
        return $this->orderBy([$this->getFieldName('createdAt') => SORT_DESC]);
    }

    /**
     * Return published session
     * @return $this
     */
    public function published()
    {
        return $this->andWhere([$this->getFieldName('isPublished') => true]);
    }
}
