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
use rest\tests\RestTester;

/**
 * Class RecoveryPasswordCest
 * @group user
 */
class RecoveryPasswordCest
{
    public $url = '/user/recovery-password';
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
        $I->sendOPTIONS('/user/recovery-password');
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @param RestTester $I
     */
    public function notExistingEmailCantBeRecovered(RestTester $I)
    {
        $I->sendPOST(
            $this->url,
            ['email' => 'notexisiting@test.com']
        );
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    /**
     * @param RestTester $I
     */
    public function deletedUserCantBeRecovered(RestTester $I)
    {
        $user = $I->grabFixture('users', UserFixture::DELETED);
        $I->sendPOST(
            $this->url,
            ['email' => $user->email]
        );
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    /**
     * @param RestTester $I
     */
    public function recoveryTokenCreated(RestTester $I)
    {
        $user = $I->grabFixture('users', UserFixture::USER);

        $I->sendPOST(
            $this->url,
            ['email' => $user->email]
        );

        // check that user passwordResetToken is Not null
        $I->dontSeeRecord(User::class, ['email' => $user->email, 'passwordResetToken' => null]);

        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }

    /**
     * @param RestTester $I
     */
    public function emailIsSend(RestTester $I)
    {
        $user = $I->grabFixture('users', UserFixture::USER);

        $I->sendPOST(
            $this->url,
            ['email' => $user->email]
        );

        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $userModel = User::findOne($user->id);
        $resetLink = '/reset-password/' . $userModel->passwordResetToken;

        $I->seeEmailIsSent();
        /** @var \yii\swiftmailer\Message $email */
        $email = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $email->getTo());
        $I->assertArrayHasKey(\Yii::$app->params['adminEmail'], $email->getFrom());
        $I->assertContains($resetLink, $email->getTextBody());
    }
}
