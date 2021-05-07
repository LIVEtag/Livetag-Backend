<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Analytics;

use common\models\Analytics\StreamSessionProductEvent;
use common\components\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Analytics\StreamSessionProductEvent]].
 *
 * @see StreamSessionProductEvent
 */
class StreamSessionProductEventQuery extends ActiveQuery
{

    /**
     * @param int $id
     * @return $this
     */
    public function byStreamSessionId(int $id): self
    {
        return $this->andWhere([$this->getFieldName('streamSessionId') => $id]);
    }

    /**
     * @param int|array $id
     * @return $this
     */
    public function byProductId($id): self
    {
        return $this->andWhere([$this->getFieldName('productId') => $id]);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function byUserId(int $id): self
    {
        return $this->andWhere([$this->getFieldName('userId') => $id]);
    }

    /**
     * @param string|array $type
     * @return $this
     */
    public function byType($type): self
    {
        return $this->andWhere([$this->getFieldName('type') => $type]);
    }

    /**
     * @return $this
     */
    public function byProductTypes()
    {
        return $this->byType([
            StreamSessionProductEvent::TYPE_PRODUCT_CREATE,
            StreamSessionProductEvent::TYPE_PRODUCT_UPDATE,
            StreamSessionProductEvent::TYPE_PRODUCT_DELETE,
        ]);
    }
}
