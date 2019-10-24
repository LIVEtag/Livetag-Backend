<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rests\tests\rest\User;

use Codeception\Example;
use Codeception\Util\Fixtures;
use Codeception\Util\HttpCode;
use common\models\User;
use rest\tests\RestTester;

/**
 * Class RegisterCest
 * @group user
 */
class RegisterCest
{
    public $verb = 'POST';
    public $url = '/user/register';

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
    public function registrationWorks(RestTester $I)
    {
        $I->sendPOST($this->url, [
            'email' => 'user2@test.com',
            'password' => 'password_0',
        ]);
        $I->seeResponseContains('token');
        $I->seeResponseStructure200();
        $I->seeResponseMatchesJsonType(
            [
                'token' => 'string',
                'expiredAt' => 'integer:>0'
            ],
            '$.result'
        );

        $I->seeRecord(User::class, ['email' => 'user2@test.com',]);
    }

    /**
     * @return array
     */
    protected function validationProvider()
    {
        $provider = require __DIR__ . '/providers/register.php';
        return $provider['validation'];
    }
}
