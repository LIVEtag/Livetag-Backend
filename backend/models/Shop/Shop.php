<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Shop;

use common\models\queries\Shop\ShopQuery;
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

    /**
     * Return entity as array that could be used in backend lists
     * @param int|array $id search by one or array of ids
     * @return array [id=>key] array of entities
     */
    public static function getIndexedArray($id): array
    {
        $query = self::getSearchQuery();
        if ($id) {
            $query->byId($id);
        }
        return $query->indexBy('id')->column();
    }

    /**
     * Prepare format for Search (in select)
     * @return ProfileQuery
     */
    public static function getSearchQuery(): ShopQuery
    {
        return self::find()->select(["name AS text", 'id']);
    }

}
