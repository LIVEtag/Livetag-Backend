<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rests\tests\rest\User;

use Codeception\Example;
use common\fixtures\UserFixture;
use rest\tests\CommonActions;
use rest\tests\RestTester;

/**
 * Class ChangePasswordCest
 * @group user
 */
class ChangePasswordCest extends CommonActions
{
    public $verb = 'PATCH';
    public $url = '/user/change-password';

    /**
     * @param RestTester $I
     * @param Example $example
     * @dataProvider validationProvider
     */
    public function validationWorks(RestTester $I, Example $example)
    {
        $I->loginAs(UserFixture::USER);
        $I->amGoingTo($example['goingTo']);
        $I->sendPATCH($this->url, $example['request']);
        $I->seeResponseStructure422();
        $I->seeResponseContainsJson($example['response']);
    }


    /**
     * @param RestTester $I
     */
    public function saveNewPasswordWorks(RestTester $I)
    {
        $I->loginAs(UserFixture::USER);
        $I->sendPATCH(
            '/user/change-password',
            [
                'password' => 'password_0',
                'newPassword' => '1234test',
                'confirmPassword' => '1234test'
            ]
        );

        $I->seeResponseStructure200();
    }

    /**
     * @return array
     */
    protected function validationProvider()
    {
        $provider = require __DIR__ . '/providers/changePassword.php';
        return $provider['validation'];
    }
}
