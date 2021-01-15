<?php
use common\fixtures\ShopFixture;
use common\fixtures\UserFixture;

return [
    [
        'userId' => UserFixture::SELLER_1,
        'shopId' => ShopFixture::STORE_1,
    ],
    [
        'userId' => UserFixture::SELLER_2,
        'shopId' => ShopFixture::STORE_2,
    ],
    [
        'userId' => UserFixture::DELETED,
        'shopId' => ShopFixture::STORE_2,
    ],
];
