<?php
namespace rest\tests\api\User;

use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;
use common\fixtures\UserFixture;

class ChangePasswordCest extends ActionCest
{
    use ProviderDataTrait;
    use AccessTestTrait;

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return self::METHOD_PATCH;
    }

    /**
     * @param ApiTester $I
     * @return string
     */
    protected function getUrl(ApiTester $I): string
    {
        return '/user/change-password';
    }

    /**
     * @param ApiTester $I
     * @throws \ReflectionException
     */
    public function update(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::USER);
        foreach ($this->getProviderData($I, 'update') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);
            $I->seeResponseResultIsNoContent();
        }
    }

    /**
     * @param ApiTester $I
     * @throws \Exception
     */
    public function validation(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::USER);
        foreach ($this->getProviderData($I, 'validation') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);
            $I->seeResponseResultIsUnprocessableEntity();
            $I->seeResponseContainsJson($data['response']);
        }
    }
}
