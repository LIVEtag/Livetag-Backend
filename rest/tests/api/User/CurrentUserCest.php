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
     * @throws \ReflectionException
     */
    public function update(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->grabFixture('users', UserFixture::SELLER_1);

        $I->amLoggedInApiAs(UserFixture::SELLER_1);
        $I->wantToTest('view current user');

            $I->send($this->getMethod(), $this->getUrl($I));
            $I->seeResponseResultIsOk();
            $I->seeResponseMatchesJsonType(
                [
                    'id' => "integer:={$user->id}",
                    'email' => "string:={$user->email}"
                ],
                '$.result'
            );
    }
}
