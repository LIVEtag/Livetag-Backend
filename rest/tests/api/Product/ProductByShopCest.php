<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Product;

use common\fixtures\ShopFixture;
use common\fixtures\UserFixture;
use rest\tests\ActionCest;
use rest\tests\ApiTester;

/**
 * @group Product
 */
class ProductByShopCest extends ActionCest
{
    /** @var integer */
    protected $shopId;

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
        return "/shop/{$this->shopUri}/product";
    }

    /**
     * @param ApiTester $I
     */
    public function successListOfProductsByShopUri(ApiTester $I)
    {
        $shop = $I->grabFixture('shops', ShopFixture::SHOP_1);
        $I->amLoggedInApiAs(UserFixture::SELLER_1);
        $this->shopUri = $shop->uri;
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getProductByShopResponse(), '$.result');
    }
}
