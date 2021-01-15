<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Product\Product;
use yii\helpers\Json;

/**
 * Class ProductFixture
 */
class ProductFixture extends ActiveFixture
{
    const PRODUCT_HIDDEN_1 = 1;
    const PRODUCT_HIDDEN_2 = 2;
    const PRODUCT_HIDDEN_3 = 3;
    const PRODUCT_HIDDEN_4 = 4;
    const PRODUCT_PRESENTED_1 = 5;
    const PRODUCT_PRESENTED_2 = 6;
    const PRODUCT_PRESENTED_3 = 7;
    const PRODUCT_PRESENTED_4 = 8;
    const PRODUCT_DISPLAYED_1 = 9;
    const PRODUCT_DISPLAYED_2 = 10;
    const PRODUCT_DISPLAYED_3 = 11;
    const PRODUCT_DISPLAYED_4 = 12;
    
    public $modelClass = Product::class;
    public $depends = [ShopFixture::class];
    
    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        
        return [
            'externalId' => $this->generator->uuid,
            'shopId' => $this->generator->randomElements([ShopFixture::STORE_1, ShopFixture::STORE_2], 1)[0],
            'title' => $this->generator->text(20),
            'photo' => $this->generator->imageUrl(),
            'link' => $this->generator->url,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
