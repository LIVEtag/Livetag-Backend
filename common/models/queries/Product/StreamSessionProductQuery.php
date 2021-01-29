<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Product;

use common\models\Product\StreamSessionProduct;
use common\components\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Product\StreamSessionProduct]].
 *
 * @see StreamSessionProduct
 */
class StreamSessionProductQuery extends ActiveQuery
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
}
