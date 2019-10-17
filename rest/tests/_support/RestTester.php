<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use Codeception\Util\HttpCode;
use Codeception\Lib\Friend;
use Codeception\Actor;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class RestTester extends Actor
{
    use _generated\RestTesterActions;

    /**
     * Login user
     * NOTE: supposed that at least accessToken and users fixtures should be defined in Cest _fixture method
     * or you could use commonUserFixtures
     * ```
     *  public function _fixtures()
     *   {
     *      return Fixtures::get('commonUserFixtures');
     *   }
     * ```
     * @param string $userFixtureKey
     */
    public function loginAs($userFixtureKey): void
    {
        $accessTokens = $this->grabFixture('accessTokens');
        if (!empty($accessTokens)) {
            foreach ($accessTokens as $accessTokenData) {
                if ($accessTokenData['userId'] == $userFixtureKey) {
                    $accessToken = $accessTokenData;
                    break;
                }
            }
        }
        if (!empty($accessToken)) {
            $this->amBearerAuthenticated($accessToken['token']);

            // Content type need to be send here because configuring default headers through
            // codeception.yml REST module headers section does not working
            $this->haveHttpHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Define custom actions here
     */
    public function seeResponseStructure422(): void
    {
        $this->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=422',
                'status' => 'string:=error',
                'result' => 'array'
            ]
        );
    }

    public function seeResponseStructure200(): void
    {
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=200',
                'status' => 'string',
                'result' => 'array'
            ]
        );
    }

    public function seeResponseStructure201(): void
    {
        $this->seeResponseCodeIs(HttpCode::CREATED);
        $this->seeResponseMatchesJsonType(
            [
                'code' => 'integer:=201',
                'status' => 'string',
                'result' => 'array'
            ]
        );
    }
}
