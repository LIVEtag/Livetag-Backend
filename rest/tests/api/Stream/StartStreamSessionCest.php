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
use rest\tests\ProviderDataTrait;

/**
 * @group Stream
 */
class StartStreamSessionCest extends ActionCest
{
    use AccessTestTrait;
    use ProviderDataTrait;

    /** @var int */
    protected $streamSessionId = StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW;

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return self::METHOD_POST;
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
     * @throws \ReflectionException
     */
    public function start(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER_1);
        $I->wantToTest('Start Stream Session');
        foreach ($this->getProviderData($I, 'start-with-rotate') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);
            $I->seeResponseResultIsOk();
            $I->seeResponseMatchesJsonType($I->getStreamSessionTokenResponse(), '$.result');
        }
    }
}
