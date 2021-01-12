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
     * Return session with active status, and not expired token
     * @return $this
     */
    public function hidden()
    {
        return $this->andWhere([$this->getFieldName('status') => Product::STATUS_HIDDEN]);
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
