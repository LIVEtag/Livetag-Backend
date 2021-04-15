<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Stream;

use common\fixtures\StreamSessionFixture;
use common\fixtures\UserFixture;
use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;

class GetStreamSessionArchiveProductsCest extends ActionCest
{
    use AccessTestTrait;

    protected $streamSessionId = StreamSessionFixture::STREAM_SESSION_7_SHOP_2_ARCHIVED;

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
        return "/stream-session/{$this->streamSessionId}/archive/product";
    }

    /**
     * @param ApiTester $I
     */
    public function listAsBuyer(ApiTester $I)
    {
        $I->wantToTest('Products of selected archive Stream Session (as Buyer)');
        $I->amLoggedInApiAs(UserFixture::BUYER_2);
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType([$I->getProductResponse()], '$.result');
    }

    /**
     * @param ApiTester $I
     */
    public function listAsSeller(ApiTester $I)
    {
        $I->wantToTest('Products of selected archive Stream Session (as Seller)');
        $I->amLoggedInApiAs(UserFixture::SELLER_2);
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType([$I->getProductResponse()], '$.result');
    }
}
