<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rests\tests\rest\User;

use common\fixtures\UserFixture;
use rest\tests\CommonActions;
use rest\tests\RestTester;

/**
 * Class CurrentUserCest
 * @group user
 */
class CurrentUserCest extends CommonActions
{
    public $verb = 'GET';
    public $url = '/user/current';

    /**
     * @param RestTester $I
     */
    public function getCurrentUserWorks(RestTester $I)
    {
        $I->loginAs(UserFixture::USER);
        $I->sendGET($this->url);
        $I->seeResponseStructure200();
        $I->seeResponseMatchesJsonType(
            [
                'id' => 'integer:>0',
                'email' => 'string:=user@test.com'
            ],
            '$.result'
        );
    }
}
