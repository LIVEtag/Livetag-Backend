<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Stream;

use common\fixtures\ShopFixture;
use common\fixtures\UserFixture;
use rest\tests\ActionCest;
use rest\tests\ApiTester;

/**
 * @group Stream
 */
class ViewStreamSessionCest extends ActionCest
{

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
        return "/{$this->shopUri}/stream-session";
    }
    /** @var integer */
    protected $shopUri;

    /**
     * @param ApiTester $I
     */
    public function viewAsBuyer(ApiTester $I)
    {
        $I->wantToTest('View Stream Session of Shop (as Buyer)');
        $shop = $I->grabFixture('shops', ShopFixture::STORE_2);
        $I->amLoggedInApiAs(UserFixture::BUYER_1);
        $this->shopUri = $shop->uri;
        $expand = 'token';
        $I->send($this->getMethod(), $this->getUrl($I) . "?expand=$expand");
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getStreamSessionResponse(), '$.result');
    }

    /**
     * @param ApiTester $I
     */
    public function viewAsSeller(ApiTester $I)
    {
        $I->wantToTest('View Stream Session of Shop (as Seller)');
        $shop = $I->grabFixture('shops', ShopFixture::STORE_2);
        $I->amLoggedInApiAs(UserFixture::SELLER_1);
        $this->shopUri = $shop->uri;
        $expand = 'token';
        $I->send($this->getMethod(), $this->getUrl($I) . "?expand=$expand");
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getStreamSessionResponse(), '$.result');
    }
}
