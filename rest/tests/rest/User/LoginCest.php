<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rests\tests\rest\User;

use Codeception\Util\Fixtures;
use Codeception\Util\HttpCode;
use common\fixtures\UserFixture;
use rest\components\validation\ErrorList;
use rest\tests\RestTester;
use Codeception\Example;

/**
 * Class LoginCest
 * @group user
 */
class LoginCest
{
    public $verb = 'POST';
    public $url = '/user/login';

    /**
     * @return array
     */
    public function _fixtures()
    {
        return Fixtures::get('commonUserFixtures');
    }

    /**
     * @param RestTester $I
     */
    public function optionsWorks(RestTester $I)
    {
        $I->haveHttpHeader('Access-Control-Request-Method', $this->verb);
        $I->sendOPTIONS($this->url);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @param RestTester $I
     * @param Example $example
     * @dataProvider validationProvider
     */
    public function validationWorks(RestTester $I, Example $example)
    {
        $I->amGoingTo($example['goingTo']);
        $I->sendPOST($this->url, $example['request']);
        $I->seeResponseStructure422();
        $I->seeResponseContainsJson($example['response']);
    }

    /**
     * @param RestTester $I
     */
    public function loginWorks(RestTester $I)
    {
        $I->sendPOST($this->url, [
            'email' => 'user@test.com',
            'password' => 'password_0',
        ]);
        $I->seeResponseContains('token');
        $I->seeResponseStructure201();
        $I->seeResponseMatchesJsonType([
            'token' => 'string',
            'expiredAt' => 'integer:>0'
        ], '$.result');
    }

    /**
     * @param RestTester $I
     */
    public function deletedUserCantLogin(RestTester $I)
    {
        $user = $I->grabFixture('users', UserFixture::DELETED);
        $I->sendPOST(
            '/user/login',
            ['email' => $user->email, 'password' => 'password_0']
        );

        $I->seeResponseStructure422();
        $I->seeResponseMatchesJsonType(
            [
                [
                    'field' => 'string:=password',
                    'message' => 'string:=Incorrect email address and/or password',
                    'code' => 'integer:=' . ErrorList::CREDENTIALS_INVALID
                ]
            ],
            '$.result'
        );
    }

    /**
     * @return array
     */
    protected function validationProvider()
    {
        $provider = require __DIR__ . '/providers/login.php';
        return $provider['validation'];
    }
}
