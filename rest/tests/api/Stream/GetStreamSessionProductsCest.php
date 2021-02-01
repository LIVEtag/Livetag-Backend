<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Stream;

use common\fixtures\StreamSessionFixture;
use common\fixtures\UserFixture;
use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;

/**
 * @group Stream
 */
class GetStreamSessionProductsCest extends ActionCest
{
    use AccessTestTrait;
    /** @var int */
    protected $streamSessionId = StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE;

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
        return '/stream-session/' . $this->streamSessionId . '/product';
    }

    /**
     * @param ApiTester $I
     */
    public function products(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER_2);
        $I->wantToTest('Get products of Stream Session');
        $expand = 'product';
        $I->send($this->getMethod(), $this->getUrl($I) . "?expand=$expand");
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getStreamSessionProductsResponse(), '$.result');
    }
}
