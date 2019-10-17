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
use common\models\User;
use rest\components\validation\ErrorList;
use rest\tests\RestTester;
use yii\base\Exception;

/**
 * Class NewPasswordCest
 * @group user
 */
class NewPasswordCest
{
    public $url = '/user/new-password';

    /**
     * Load fixtures before db transaction begin
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
        $I->haveHttpHeader('Access-Control-Request-Method', 'POST');
        $I->sendOPTIONS($this->url);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * Common method
     * @return null|User
     * @throws Exception
     */
    private function createUserWithExpiredResetToken(RestTester $I)
    {
        $userFixture = $I->grabFixture('users', UserFixture::USER);

        // create invalid reset token
        $user = User::findOne(['id' => $userFixture->id]);
        $user->passwordResetToken = \Yii::$app->security->generateRandomString() . '_' . strtotime('-2 days');
        $user->save();

        return $user;
    }

    /**
     * Common method
     * @return null|User
     * @throws Exception
     */
    private function createUserWithValidResetToken(RestTester $I)
    {
        $userFixture = $I->grabFixture('users', UserFixture::USER);

        // create invalid reset token
        $user = User::findOne(['id' => $userFixture->id]);
        $user->passwordResetToken = \Yii::$app->security->generateRandomString() . '_' . time();
        $user->save();

        return $user;
    }

    /**
     * @param RestTester $I
     */
    public function checkResetTokenCannotBeBlank(RestTester $I)
    {
        $I->sendPOST(
            $this->url,
            ['resetToken' => '']
        );
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    /**
     * @param RestTester $I
     * @throws Exception
     */
    public function expiredTokenCantBeRecovered(RestTester $I)
    {
        $user = $this->createUserWithExpiredResetToken($I);

        $I->sendPOST(
            $this->url,
            ['resetToken' => $user->passwordResetToken, 'password' => '1234Test', 'confirmPassword' => '1234Test']
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    /**
     * @param RestTester $I
     */
    public function validatePasswordsCannotBeBlank(RestTester $I)
    {
        $user = $this->createUserWithValidResetToken($I);
        $I->sendPOST(
            $this->url,
            [
                'resetToken' => $user->passwordResetToken,
                'password' => '',
                'confirmPassword' => ''
            ]
        );

        $I->seeResponseStructure422();
        $I->seeResponseMatchesJsonType(
            [
                [
                    'field' => 'string:=password',
                    'message' => 'string:=Password cannot be blank.',
                    'code' => 'integer:=' . ErrorList::REQUIRED_INVALID
                ],
                [
                    'field' => 'string:=confirmPassword',
                    'message' => 'string:=Confirm Password cannot be blank.',
                    'code' => 'integer:=' . ErrorList::REQUIRED_INVALID
                ]

            ],
            '$.result'
        );
    }

    /**
     * @param RestTester $I
     * @throws Exception
     */
    public function validatePasswordsDontMatch(RestTester $I)
    {
        $user = $this->createUserWithValidResetToken($I);

        $I->sendPOST(
            $this->url,
            ['resetToken' => $user->passwordResetToken, 'password' => '1234Test', 'confirmPassword' => '12345678Test']
        );

        $I->seeResponseStructure422();
        $I->seeResponseMatchesJsonType(
            [
                [
                    'field' => 'string:=password',
                    'message' => 'string:=Password must be equal to "Confirm Password".',
                    'code' => 'integer:=' . ErrorList::COMPARE_EQUAL
                ]

            ],
            '$.result'
        );
    }

    /**
     * @param RestTester $I
     * @throws Exception
     */
    public function saveNewPasswordWorks(RestTester $I)
    {
        $user = $I->grabFixture('users', UserFixture::USER);
        $I->sendPOST(
            '/user/recovery-password',
            ['email' => $user->email]
        );

        $userModel = User::find()->where(['email' => $user->email])->one();

        $I->sendPOST(
            $this->url,
            ['resetToken' => $userModel->passwordResetToken, 'password' => '1234Test', 'confirmPassword' => '1234Test']
        );
        $I->seeResponseStructure200();

        $I->seeRecord(User::class, ['id' => $user->id, 'passwordResetToken' => null]);

        $I->amGoingTo("Check than I can login with new password");
        $I->sendPOST(
            '/user/login',
            ['email' => $user->email, 'password' => '1234Test']
        );
        $I->seeResponseStructure201();
    }
}
