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
class GetStreamSessionTokenCest extends ActionCest
{
    use AccessTestTrait;
    /** @var int */
    protected $streamSessionId = StreamSessionFixture::STREAM_ACTIVE;

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
        return '/stream-session/' . $this->streamSessionId . '/token';
    }

    /**
     * @param ApiTester $I
     */
    public function token(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER_2);
        $I->wantToTest('Get token for Stream Session');
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getStreamSessionTokenResponse(), '$.result');
    }
}
