<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Product\Product;

/**
 * Class ProductFixture
 */
class ProductFixture extends ActiveFixture
{
    const STORE_1 = 1;
    const STORE_2 = 2;
    
    public $modelClass = Product::class;
    public $depends = ['common\tests\fixtures\ShopFixture'];
    
    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'externalId' => $this->generator->uuid,
            'shopId' => 1,
            'title' => $this->generator->title,
            'options' => "{'price': {$this->generator->numberBetween(1, 1000)} }",
            'photo' => $this->generator->imageUrl(),
            'link' => $this->generator->url,
            'status' => Product::STATUS_DISPLAYED,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
