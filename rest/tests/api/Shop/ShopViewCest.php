<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Shop;

use common\fixtures\ShopFixture;
use rest\tests\ActionCest;
use rest\tests\ApiTester;

/**
 * @group Shop
 */
class ShopViewCest extends ActionCest
{
    /** @var string */
    protected $shopUri;

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return self::METHOD_GET;
    }

    /**
     * @param ApiTester $I
     * @return string
     */
    protected function getUrl(ApiTester $I): string
    {
        return "/shop/{$this->shopUri}";
    }

    /**
     * @param ApiTester $I
     */
    public function successGetShopDetailByUri(ApiTester $I)
    {
        $shop = $I->grabFixture('shops', ShopFixture::SHOP_1);
        $this->shopUri = $shop->uri;
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getShopResponse(), '$.result');
    }
}
