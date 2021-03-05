<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\User;

use backend\models\Shop\Shop;
use common\models\queries\Shop\ShopQuery;
use common\models\User as BaseModel;
use yii\helpers\ArrayHelper;

/**
 * Represents the backend version of `common\models\User`.
 */
class User extends BaseModel
{

    /**
     * @return string
     */
    public function getRoleName(): ?string
    {
        return ArrayHelper::getValue(self::ROLES, $this->role);
    }

    /**
     * @return string
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }

     /**
     * @return ShopQuery
     */
    public function getShop(): ShopQuery
    {
        return $this->hasOne(Shop::class, ['id' => 'shopId'])->viaTable('user_shop', ['userId' => 'id']);
    }
}
