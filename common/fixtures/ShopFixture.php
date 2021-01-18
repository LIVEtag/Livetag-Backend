<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Shop\Shop;

/**
 * Class ShopFixture
 */
class ShopFixture extends ActiveFixture
{
    const STORE_1 = 1;
    const STORE_2 = 2;

    public $modelClass = Shop::class;

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'name' => $this->generator->company,
            'uri' => $this->generator->slug,
            'website' => $this->generator->url,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
