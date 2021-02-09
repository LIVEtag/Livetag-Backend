<?php
use common\fixtures\ShopFixture;

return [
    ShopFixture::SHOP_1 => [
        'id' => ShopFixture::SHOP_1,
        'uri' => 'shop1',
        'website' => getenv('DEMO_SHOP_URL') ?: $this->generator->url
    ],
    ShopFixture::SHOP_2 => [
        'id' => ShopFixture::SHOP_2,
        'uri' => 'shop2',
    ],
    ShopFixture::SHOP_3 => [
        'id' => ShopFixture::SHOP_3,
        'uri' => 'shop3',
    ],
];
