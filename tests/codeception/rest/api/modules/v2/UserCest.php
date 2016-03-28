<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\api\modules\v2;

use Codeception\Scenario;
use tests\codeception\rest\ApiTester;

/**
 * Class UserCest
 */
class UserCest
{
    const TEST_EMAIL = 'v2test@test.loc';

    const TEST_PASSWORD = '123123q';

    const TEST_NAME = 'v2username';

    /**
     * Test user creation
     *
     * @param ApiTester $I
     * @param Scenario $scenario
     */
    public function testCreate(ApiTester $I, Scenario $scenario)
    {
        $I->wantTo('Create a user via API V2');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('User-Agent', 'Test-User-Agent');

        $I->sendPOST(
            'v2/users',
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
                'password' => self::TEST_PASSWORD,
            ]
        );

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
            ]
        );
        $I->seeResponseJsonMatchesJsonPath('$.accessToken.token');
        $I->seeResponseJsonMatchesJsonPath('$.accessToken.expired_at');
    }

    /**
     * Test view current user bearer authenticated
     *
     * @param ApiTester $I
     * @param Scenario $scenario
     */
    public function testCurrentBearerAuthenticated(ApiTester $I, Scenario $scenario)
    {
        $I->wantTo('View a user via API V2 bearer authenticated');

        $user = $I->getFixture('user')[0];
        $accessToken = $I->getFixture('access_token')[0];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('User-Agent', 'Test-User-Agent');

        $I->amBearerAuthenticated($accessToken['token']);

        $I->sendGET('v2/users/current');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => $user['username'],
                'email' => $user['email'],
                'accessToken' => [
                    'token' => $accessToken['token'],
                    'expired_at' => $accessToken['expired_at'],
                ]
            ]
        );
    }
}
