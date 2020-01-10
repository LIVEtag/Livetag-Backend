<?php
namespace rest\tests\api\User;

use rest\tests\ProviderDataTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;
use common\models\AccessToken;

class LoginCest extends ActionCest
{
    use ProviderDataTrait;

    protected function getMethod(): string
    {
        return self::METHOD_POST;
    }

    protected function getUrl(ApiTester $I): string
    {
        return '/user/login';
    }

    /**
     * @param ApiTester $I
     * @throws \Exception
     */
    public function validation(ApiTester $I)
    {
        foreach ($this->getProviderData($I, 'validation') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);
            $I->seeResponseResultIsUnprocessableEntity();
            $I->seeResponseContainsJson($data['response']);
        }
    }

    /**
     * @param ApiTester $I
     * @throws \Exception
     */
    public function login(ApiTester $I)
    {
        foreach ($this->getProviderData($I, 'login') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);

            $I->seeResponseResultIsCreated();
            $I->seeResponseMatchesJsonType($data['response'], '$.result');
        }

        $token = $I->grabDataFromResponseByJsonPath('$.result.token')[0];
        $I->seeRecord(AccessToken::class, [
            'token' => $token
        ]);
    }
}
