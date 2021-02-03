<?php

namespace rest\tests\api\User;

use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;

class CurrentUserCest extends ActionCest
{
    use AccessTestTrait;
    use ProviderDataTrait;

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
    public function current(ApiTester $I)
    {
        foreach ($this->getProviderData($I, 'current') as $data) {
            $this->dataComment($I, $data);
            $I->amLoggedInApiAs($data['userId']);
            $I->send($this->getMethod(), $this->getUrl($I));
            $I->seeResponseResultIsOk();
            $I->seeResponseMatchesJsonType($I->getUserResponse(), '$.result');
        }
    }
}
