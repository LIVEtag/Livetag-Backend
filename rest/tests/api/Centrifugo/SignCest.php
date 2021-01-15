<?php

namespace rest\tests\api\Centrifugo;

use common\fixtures\UserFixture;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;
use rest\tests\ActionCest;

/**
 * Class SignCest
 * @package rest\tests\api\Centrifugo
 * @group centri
 */
class SignCest extends ActionCest
{
    use ProviderDataTrait;

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
        return '/centrifugo/sign';
    }

    /**
     * @param ApiTester $I
     * @throws \Exception
     */
    public function sign(ApiTester $I)
    {
        $I->wantToTest('Get centrifugo token');

        $I->amLoggedInApiAs(UserFixture::SELLER_1);

        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType(['token' => 'string'], '$.result');
    }
}
