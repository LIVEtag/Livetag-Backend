<?php
declare(strict_types=1);
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rests\tests\rest\User;

use Codeception\Util\Fixtures;
use Codeception\Util\HttpCode;
use common\fixtures\UserFixture;
use rest\tests\RestTester;

/**
 * Class LogoutCest
 */
class LogoutCest
{
    public $verb = 'POST';
    public $url = '/user/logout';

    /**
     * Load fixtures before db transaction begin
     * @return array
     */
    public function _fixtures()
    {
        /**
         * @see /rest/tests/rest/_bootstrap.php
         */
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
     */
    public function guestCantLogout(RestTester $I)
    {
        $I->sendPOST($this->url);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseMatchesJsonType(
            [
                'name' => 'string:=Unauthorized',
                'message' => 'string:=Your request was made with invalid credentials.',
                'code' => 'integer:=401',
                'status' => 'string:=error'
            ]
        );
    }

    /**
     * @param RestTester $I
     */
    public function logoutWorks(RestTester $I)
    {
        $I->loginAs(UserFixture::USER);
        $response = $I->sendPOST($this->url);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
        $I->assertNull($response);

        $I->sendGET('/user/current');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
