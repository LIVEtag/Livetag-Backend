<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\api\modules\v2;

use Codeception\Scenario;
use rest\common\models\AccessToken;
use rest\common\models\User;
use rest\common\models\views\User\SignupUser;
use tests\codeception\rest\ApiTester;

/**
 * Class AccessTokenCest
 */
class AccessTokenCest
{
    const TEST_EMAIL = 'test@test.com';

    const TEST_PASSWORD = '123123q';

    const TEST_NAME = 'test';

    /**
     * @param ApiTester $I
     */
    public function _before(ApiTester $I)
    {
        $user = User::find()->where(['email' => self::TEST_EMAIL])
            ->one();
        if ($user) {
            AccessToken::deleteAll(['user_id' => $user->getId()]);
            User::deleteAll(['email' => self::TEST_EMAIL]);
        }
    }

    /**
     * @param ApiTester $I
     */
    public function _after(ApiTester $I)
    {
        $user = User::find()->where(['email' => self::TEST_EMAIL])
            ->one();
        AccessToken::deleteAll(['user_id' => $user->getId()]);
        User::deleteAll(['email' => self::TEST_EMAIL]);
    }

    /**
     * Test user token creation
     *
     * @param ApiTester $I
     * @param Scenario $scenario
     */
    public function createTest(ApiTester $I, Scenario $scenario)
    {
        $user = $this->createUser();

        $I->wantTo('Create a user via API V2');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            'v1/access-tokens',
            [
                'username' => self::TEST_NAME,
                'password' => self::TEST_PASSWORD
            ]
        );

        /** @var AccessToken $token */
        $token = AccessToken::find()->andWhere(['user_id' => $user->getId()])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'token' => $token->token,
                'expired_at' => $token->expired_at,
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
