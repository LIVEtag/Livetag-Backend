<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use common\fixtures\ProductFixture;
use common\fixtures\StreamSessionFixture;
use common\fixtures\UserFixture;
use common\models\Analytics\StreamSessionProductEvent;

$product = $I->grabFixture('products', ProductFixture::PRODUCT_9_SHOP_2);

return [
    'create' => [
        [
            'dataComment' => 'Add to Cart',
            'userId' => UserFixture::BUYER_2,
            'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
            'productId' => $product->id,
            'request' => [
                'data' => [
                    'type' => StreamSessionProductEvent::TYPE_ADD_TO_CART,
                    'payload' => $product->options[0]
                ]
            ]
        ]
    ],
];
