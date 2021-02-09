<?php
/**
 * Copyright Â© 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use common\fixtures\AccessTokenFixture;
use common\fixtures\CommentFixture;
use common\fixtures\ProductFixture;
use common\fixtures\ShopFixture;
use common\fixtures\StreamSessionFixture;
use common\fixtures\StreamSessionProductFixture;
use common\fixtures\StreamSessionTokenFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserShopFixture;

trait BaseFixtureTrait
{

    /**
     * Load fixtures before db transaction begin
     * @return array
     */
    public function _fixtures()
    {
        return [
            'users' => UserFixture::class,
            'accessTokens' => AccessTokenFixture::class,
            'shops' => ShopFixture::class,
            'userShops' => UserShopFixture::class,
            'streamSessions' => StreamSessionFixture::class,
            'streamSessionTokens' => StreamSessionTokenFixture::class,
            'products' => ProductFixture::class,
            'streamSessionProducts' => StreamSessionProductFixture::class,
            'comments' => CommentFixture::class,
        ];
    }
}
