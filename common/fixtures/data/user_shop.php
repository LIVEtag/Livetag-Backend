<?php
use common\fixtures\ShopFixture;
use common\fixtures\UserFixture;

return [
    [
        'userId' => UserFixture::SELLER_1,
        'shopId' => ShopFixture::SHOP_1,
    ],
    [
        'userId' => UserFixture::SELLER_2,
        'shopId' => ShopFixture::SHOP_2,
    ],
    [
        'userId' => UserFixture::SELLER_3,
        'shopId' => ShopFixture::SHOP_3,
    ],
    [
        'userId' => UserFixture::DELETED,
        'shopId' => ShopFixture::SHOP_2,
    ],
];
