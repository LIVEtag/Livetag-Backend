<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;

/**
 * Class UserShopFixture
 */
class UserShopFixture extends ActiveFixture
{
    public $tableName = '{{%user_shop}}';

    public $depends = [
        UserFixture::class,
        ShopFixture::class,
    ];

    public $requiredAttributes = [
        'userId',
        'shopId',
    ];

    protected function getTemplate(): array
    {
        return [];
    }
}
