<?php
namespace rest\tests\api\User;

use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;
use common\fixtures\UserFixture;

class LogoutCest extends ActionCest
{
    use AccessTestTrait;

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
        return '/user/logout';
    }

    /**
     * @param ApiTester $I
     */
    public function update(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER_1);
        $I->wantToTest('logout');

        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsNoContent();

        $I->sendGET('/user/current');
        $I->seeResponseResultIsUnauthorized();
    }
}
