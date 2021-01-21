<?php
namespace rest\tests\api\User;

use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;
use common\models\User;
use common\fixtures\UserFixture;

class CurrentUserCest extends ActionCest
{
    use AccessTestTrait;

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
        return '/user/current';
    }

    /**
     * @param ApiTester $I
     */
    public function currentSeller(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->grabFixture('users', UserFixture::SELLER_1);
        $I->amLoggedInApiAs(UserFixture::SELLER_1);
        $I->wantToTest('View current Seller');

        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType(
            [
                'role' => "string:={$user->role}",
                'name' => "string:={$user->shop->name}"
            ],
            '$.result'
        );
    }

    /**
     * @param ApiTester $I
     */
    public function currentBuyer(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->grabFixture('users', UserFixture::BUYER_1);
        $I->amLoggedInApiAs(UserFixture::BUYER_1);
        $I->wantToTest('View current Buyer');

        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType(
            [
                'role' => "string:={$user->role}",
                'name' => "string:={$user->name}"
            ],
            '$.result'
        );
    }
}
