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
     * Return session with active status, and not expired token
     * @return $this
     */
    public function active()
    {
        return $this->andWhere([$this->getFieldName('status') => Shop::STATUS_ACTIVE]);
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