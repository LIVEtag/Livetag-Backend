<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Stream;

use common\fixtures\ShopFixture;
use common\fixtures\UserFixture;
use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;

class StreamSessionsCest extends ActionCest
{
    use AccessTestTrait;

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
        return "/shop/{$this->shopUri}/stream-session";
    }

    /**
     * @param ApiTester $I
     */
    public function listAsBuyer(ApiTester $I)
    {
        $I->wantToTest('List Stream Session of Shop (as Buyer)');
        $shop = $I->grabFixture('shops', ShopFixture::SHOP_2);
        $I->amLoggedInApiAs(UserFixture::BUYER_2);
        $this->shopUri = $shop->uri;
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType([$I->getStreamSessionResponse()], '$.result');
    }

    /**
     * @param ApiTester $I
     */
    public function listAsSeller(ApiTester $I)
    {
        $I->wantToTest('List Stream Session of Shop (as Seller)');
        $shop = $I->grabFixture('shops', ShopFixture::SHOP_2);
        $I->amLoggedInApiAs(UserFixture::SELLER_2);
        $this->shopUri = $shop->uri;
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType([$I->getStreamSessionResponse()], '$.result');
    }
}
