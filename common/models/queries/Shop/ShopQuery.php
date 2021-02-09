<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Shop;

use common\components\db\ActiveQuery;
use common\models\Shop\Shop;

/**
 * This is the ActiveQuery class for [[\common\models\Shop\Shop]].
 *
 * @see Shop
 */
class ShopQuery extends ActiveQuery
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
     * @param string $uri
     * @return $this
     */
    public function byUri(string $uri): self
    {
        return $this->andWhere([$this->getFieldName('uri') => $uri]);
    }
}
