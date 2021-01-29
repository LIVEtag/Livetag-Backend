<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Product\StreamSessionProduct;

/**
 * Class StreamSessionProductFixture
 */
class StreamSessionProductFixture extends ActiveFixture
{
    //New Session of Shop1 (5/8 products)
    const PRODUCT_1_SESSION_3 = 1;
    const PRODUCT_2_SESSION_3 = 2;
    const PRODUCT_3_SESSION_3 = 3;
    const PRODUCT_4_SESSION_3 = 4;
    const PRODUCT_5_SESSION_3 = 5;
    //Active Session of Shop2 (4/4 products)
    const PRODUCT_9_SESSION_4 = 6;
    const PRODUCT_10_SESSION_4 = 7;
    const PRODUCT_11_SESSION_4 = 8;
    const PRODUCT_12_SESSION_4 = 9;

    public $modelClass = StreamSessionProduct::class;
    public $depends = [
        ProductFixture::class,
        StreamSessionFixture::class,
    ];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'status' => StreamSessionProduct::STATUS_DISPLAYED,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
