<?php
namespace rest\tests\api\User;

use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;
use common\components\validation\ErrorList;
use common\fixtures\UserFixture;
use common\models\User;
use yii\base\Exception;

class NewPasswordCest extends ActionCest
{
    use ProviderDataTrait;

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return self::METHOD_POST;
    }

    /**
     * @param ApiTester $I
     * @return string
     */
    protected function getUrl(ApiTester $I): string
    {
        return '/user/new-password';
    }

    /**
     * @param ApiTester $I
     * Common method
     * @return null|User
     * @throws Exception
     */
    private function createUserWithValidResetToken(ApiTester $I)
    {
        $userFixture = $I->grabFixture('users', UserFixture::USER);

        // create valid reset token
        $user = User::findOne(['id' => $userFixture->id]);
        $user->passwordResetToken = \Yii::$app->security->generateRandomString() . '_' . time();
        $user->save();

        return $user;
    }

    /**
     * @param ApiTester $I
     * @throws \ReflectionException
     */
    public function resetTokenValidation(ApiTester $I)
    {
        foreach ($this->getProviderData($I, 'tokenValidation') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);
            $I->seeResponseResultIsNotFound();
        }
    }

    /**
     * @param ApiTester $I
     * @throws \Exception
     */
    public function validatePasswordsCannotBeBlank(ApiTester $I)
    {
        $user = $this->createUserWithValidResetToken($I);
        $I->amGoingTo('password cannot be blank');

        $I->send(
            $this->getMethod(),
            $this->getUrl($I),
            [
                'resetToken' => $user->passwordResetToken,
                'password' => '',
                'confirmPassword' => ''
            ]
        );

        $I->seeResponseResultIsUnprocessableEntity();
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
     * @param ApiTester $I
     * @throws \Exception
     */
    public function validatePasswordsDontMatch(ApiTester $I)
    {
        $user = $this->createUserWithValidResetToken($I);

        $password = $I->generator->password(8, 15);
        $I->amGoingTo('password do not match');

        $I->send(
            $this->getMethod(),
            $this->getUrl($I),
            [
                'resetToken' => $user->passwordResetToken,
                'password' => $password,
                'confirmPassword' => $password . $password
            ]
        );

        $I->seeResponseResultIsUnprocessableEntity();
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
     * @param ApiTester $I
     * @throws \Exception
     */
    public function saveNewPasswordWorks(ApiTester $I)
    {
        $user = $I->grabFixture('users', UserFixture::USER);
        $password = $I->generator->password(8, 15);
        $I->wantToTest('create new password');

        $I->send(
            $this->getMethod(),
            '/user/recovery-password',
            [
                'email' => $user->email
            ]
        );
        /** @var User $userToken */
        $userToken = $I->grabRecord(User::class, ['id' => $user->id]);
        $I->send(
            $this->getMethod(),
            $this->getUrl($I),
            [
                'resetToken' => $userToken->passwordResetToken,
                'password' => $password,
                'confirmPassword' => $password
            ]
        );
        $I->seeResponseResultIsNoContent();
        $I->seeRecord(User::class, ['id' => $user->id, 'passwordResetToken' => null]);

        $I->amGoingTo("Check than I can login with new password");
        $I->send(
            $this->getMethod(),
            '/user/login',
            [
                'email' => $user->email,
                'password' => $password
            ]
        );
        $I->seeResponseResultIsCreated();
    }
}
