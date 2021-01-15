<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Stream;

use common\fixtures\ShopFixture;
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
        return "/{$this->shopId}/stream-session";
    }
    /** @var integer */
    protected $shopId;

    /**
     * @param ApiTester $I
     */
    public function view(ApiTester $I)
    {
        $I->wantToTest('View Stream Session of Shop (for guest)');
        $this->shopId = ShopFixture::STORE_2;
        $expand = 'token';
        $I->send($this->getMethod(), $this->getUrl($I) . "?expand=$expand");
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getStreamSessionResponse(), '$.result');
    }
}
