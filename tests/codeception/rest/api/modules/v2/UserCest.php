<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\api\modules\v2;

use Codeception\Scenario;
use rest\common\models\User;
use rest\common\models\views\User\SignupUser;
use tests\codeception\rest\ApiTester;

/**
 * Class UserCest
 */
class UserCest
{
    const TEST_EMAIL = 'test@test.com';

    const TEST_PASSWORD = '123123q';

    const TEST_NAME = 'username';

    /**
     * @param ApiTester $I
     */
    public function _before(ApiTester $I)
    {
        User::deleteAll(['email' => self::TEST_EMAIL]);
    }

    /**
     * @param ApiTester $I
     */
    public function _after(ApiTester $I)
    {
        User::deleteAll(['email' => self::TEST_EMAIL]);
    }

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

        $I->sendPOST(
            'v2/users',
            [
                'username' => 'test',
                'email' => self::TEST_EMAIL,
                'password' => self::TEST_PASSWORD
            ]
        );

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();

        /** @var User $user */
        $user = User::findOne(['email' => self::TEST_EMAIL]);
        $I->seeResponseContainsJson(
            [
                'username' => 'test',
                'email' => self::TEST_EMAIL,
                'accessToken' => [
                    'token' => $user->getAccessToken()->token,
                    'expired_at' => $user->getAccessToken()->expired_at,
                ]
            ]
        );
    }

    /**
     * Test view current user http authenticated
     *
     * @param ApiTester $I
     * @param Scenario $scenario
     */
    public function testCurrentHttpAuthenticated(ApiTester $I, Scenario $scenario)
    {
        $user = $this->createUser();

        $I->wantTo('View a user via API V2 http authenticated');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amHttpAuthenticated($user->username, self::TEST_PASSWORD);

        $I->sendGET('v2/users/current');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
                'accessToken' => [
                    'token' => $user->getAccessToken()->token,
                    'expired_at' => $user->getAccessToken()->expired_at,
                ]
            ]
        );
    }

    /**
     * Test view current user bearer authenticated
     *
     * @param ApiTester $I
     * @param Scenario $scenario
     */
    public function testCurrentBearerAuthenticated(ApiTester $I, Scenario $scenario)
    {
        $user = $this->createUser();

        $I->wantTo('View a user via API V2 bearer authenticated');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Cookie', 'PHPSTORM');
        $I->amBearerAuthenticated($user->getAccessToken()->token);

        $I->sendGET('v2/users/current');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
                'accessToken' => [
                    'token' => $user->getAccessToken()->token,
                    'expired_at' => $user->getAccessToken()->expired_at,
                ]
            ]
        );
    }

    /**
     * Create user
     *
     * @return User
     */
    protected function createUser()
    {
        $user = new SignupUser();
        $user->load(
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
                'password' => self::TEST_PASSWORD,
                'userAgent' => 'test-user-agent',
                'userIp' => '0.0.0.0',
            ],
            ''
        );

        return $user->signup();
    }
}
