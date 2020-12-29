<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Shop;

use common\models\Shop\Shop as BaseModel;
use yii\helpers\ArrayHelper;

/**
 * Represents the backend version of `common\models\Shop\Shop`.
 */
class Shop extends BaseModel
{

    /**
     * @return string
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }
}
