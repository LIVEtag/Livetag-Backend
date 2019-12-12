<?php
/**
 * Copyright Â© 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use common\fixtures\UserFixture;
use common\fixtures\AccessTokenFixture;

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
        ];
    }
}
