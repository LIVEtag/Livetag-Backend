<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use Codeception\Util\Fixtures;
use Codeception\Util\HttpCode;

/**
 * Class CommonActions
 */
class CommonActions
{
    public $url;
    public $verb = 'GET';
    public $commonUserFixtures = 'commonUserFixtures';

    /**
     * Load fixtures before db transaction begin
     * @return array
     */
    public function _fixtures()
    {
        /**
         * @see /rest/tests/rest/_bootstrap.php
         */
        return Fixtures::get($this->commonUserFixtures);
    }

    /**
     * @param RestTester $I
     */
    public function optionsWorks(RestTester $I)
    {
        $I->haveHttpHeader('Access-Control-Request-Method', $this->verb);
        $I->sendOPTIONS($this->getUrl($I));
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @param RestTester $I
     */
    public function guestCantAccess(RestTester $I)
    {
        $I->{$this->getSendMethod()}($this->getUrl($I));
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
     * Create send method according to verb
     * @return string
     */
    private function getSendMethod()
    {
        return 'send' . strtoupper($this->verb);
    }

    /**
     * @return mixed
     * @SuppressWarnings(PHPMD)
     */
    protected function getUrl()
    {
        return $this->url;
    }
}
