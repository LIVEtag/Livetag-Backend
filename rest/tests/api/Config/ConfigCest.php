<?php

namespace rest\tests\api\Anybody\Application;

use Exception;
use rest\tests\ActionCest;
use rest\tests\ApiTester;

/**
 * Class ConfigCest
 */
class ConfigCest extends ActionCest
{

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
        return '/config';
    }

    /**
     * @param ApiTester $I
     * @throws Exception
     */
    public function config(ApiTester $I)
    {
        $I->wantToTest('Get the app config');

        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType(
            [
                'version' => [
                    'major' => 'integer',
                ],
                'parameters' => [
                    'vonage' => ['apiKey' => 'string'],
                    'centrifugo' => ['ws' => 'string']
                ],
                'errors' => [],
            ],
            '$.result'
        );
    }
}
