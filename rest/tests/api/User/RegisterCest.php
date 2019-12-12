<?php
namespace rest\tests\api\User;

use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;

class RegisterCest extends ActionCest
{
    use ProviderDataTrait;

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
        return '/user/register';
    }

    /**
     * @param ApiTester $I
     * @throws \ReflectionException
     */
    public function create(ApiTester $I)
    {
        foreach ($this->getProviderData($I, 'create') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);
            $I->seeResponseResultIsOk();
            $I->seeResponseMatchesJsonType($data['response'], '$.result');
        }
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
}
