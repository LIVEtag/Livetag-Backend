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
class StopStreamSessionCest extends ActionCest
{
    use AccessTestTrait;
    /** @var int */
    protected $streamSessionId = StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE;

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return self::METHOD_DELETE;
    }

    /**
     * @param ApiTester $I
     * @return string
     */
    protected function getUrl(ApiTester $I): string
    {
        return '/stream-session/' . $this->streamSessionId;
    }

    /**
     * @param ApiTester $I
     */
    public function stop(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER_2);
        $I->wantToTest('Stop Stream Session');
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsNoContent();
    }
}
