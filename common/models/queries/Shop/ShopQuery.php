<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Shop;

use common\components\db\ActiveQuery;
use common\models\Shop\Shop;

/**
 * Class ShopQuery
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
}
