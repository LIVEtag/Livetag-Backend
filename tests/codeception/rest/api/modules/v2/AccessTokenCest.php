<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\api\modules\v2;

use Codeception\Scenario;
use tests\codeception\rest\ApiTester;

/**
 * Class AccessTokenCest
 */
class AccessTokenCest
{
    /**
     * Test user token creation
     *
     * @param ApiTester $I
     * @param Scenario $scenario
     */
    public function createTest(ApiTester $I, Scenario $scenario)
    {
        $I->wantTo('Create a user via API V2');

        $user = $I->getFixture('user')[0];
        $accessToken = $I->getFixture('access_token')[0];

        $I->haveHttpHeader('User-Agent', 'Test-User-Agent');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            'v2/access-tokens',
            [
                'username' => $user['username'],
                'password' => 'password_0'
            ]
        );

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'token' => $accessToken['token'],
                'expired_at' => $accessToken['expired_at'],
            ]
        );
    }
}
