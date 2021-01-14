<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Product;

use common\components\db\ActiveQuery;
use common\models\Product\Product;

/**
 * This is the ActiveQuery class for [[\common\models\Product\Product]].
 *
 * @see Product
 */
class ProductQuery extends ActiveQuery
{
    /**
     * @param int $status
     * @return $this
     */
    public function byStatus(int $status): self
    {
        return $this->andWhere([$this->getFieldName('status') => $status]);
    }

    /**
     * @param int|array $id
     * @return $this
     */
    public function byId($id): self
    {
        return $this->andWhere([$this->getFieldName('id') => $id]);
    }
}
