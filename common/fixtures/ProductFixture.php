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
    const PRODUCT_1 = 1;
    const PRODUCT_2 = 2;
    
    public $modelClass = Product::class;
    public $depends = [ShopFixture::class];
    
    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        
        return [
            'externalId' => $this->generator->incrementalTime,
            'shopId' => self::PRODUCT_1,
            'title' => $this->generator->text(20),
            'options' => Json::encode([
                'price'=> $this->generator->numberBetween(1, 1000),
                'color'=> $this->generator->colorName,
                'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0]
            ]),
            'photo' => $this->generator->imageUrl(),
            'link' => $this->generator->url,
            'status' => $this->generator->randomElements(Product::STATUSES_CODES, 1)[0],
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
