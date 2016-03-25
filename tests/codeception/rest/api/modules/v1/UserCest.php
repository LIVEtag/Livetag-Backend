<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\api\modules\v1;

use Codeception\Scenario;
use rest\common\models\AccessToken;
use rest\common\models\User;
use rest\common\models\views\AccessToken\CreateToken;
use rest\common\models\views\User\SignupUser;
use tests\codeception\rest\ApiTester;

/**
 * Class UserCest
 */
class UserCest
{
    const TEST_EMAIL = 'test@test.com';

    const TEST_PASSWORD = '123123q';

    const TEST_NAME = 'test';

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
        $I->wantTo('Create a user via API V1');
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST(
            'v1/users',
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
                'password' => self::TEST_PASSWORD
            ]
        );

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();

        /** @var User $user */
        $user = User::findOne(['email' => self::TEST_EMAIL]);
        $token = $user->getAccessToken()->one();

        $I->seeResponseContainsJson(
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
                'accessToken' => [
                    'token' => $token->token,
                    'expired_at' => $token->expired_at,
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

        // Creating a token to validate the ratio of the current user token
        $this->createAccessToken($user);

        $token = $user->getAccessToken()->one();

        // Creating a token to validate the ratio of the current user token
        $this->createAccessToken($user);

        $I->wantTo('View a user via API V1 bearer authenticated');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('User-Agent', 'Test-User-Agent');

        $I->amBearerAuthenticated($token->token);

        $I->sendGET('v1/users/current');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => self::TEST_NAME,
                'email' => self::TEST_EMAIL,
                'accessToken' => [
                    'token' => $token->token,
                    'expired_at' => $token->expired_at,
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
                'userAgent' => 'Test-User-Agent',
                'userIp' => '',
            ],
            ''
        );

        return $user->signup();
    }

    /**
     * @param User $user
     * @return bool|AccessToken
     */
    private function createAccessToken(User $user)
    {
        $accessTokenCreate = new CreateToken(
            [
                'username' => $user->username,
                'password' => self::TEST_PASSWORD,
                'userAgent' => 'Test-User-Agent',
                'userIp' => '',
            ]
        );
        return $accessTokenCreate->create();
    }
}
