<?php
namespace rest\tests\api\User;

use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;
use common\fixtures\UserFixture;
use common\models\User;

class RecoveryPasswordCest extends ActionCest
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
        return '/user/recovery-password';
    }

    /**
     * @param ApiTester $I
     * @throws \Exception
     */
    public function recovery(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER);
        $user = $I->grabFixture('users', UserFixture::SELLER);

        $I->send($this->getMethod(), $this->getUrl($I), ['email' => $user->email]);
        $I->seeResponseResultIsNoContent();

        // check that user passwordResetToken is Not null
        $I->dontSeeRecord(User::class, ['email' => $user->email, 'passwordResetToken' => null]);
    }

    /**
     * @param ApiTester $I
     * @throws \Exception
     */
    public function validation(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER);
        foreach ($this->getProviderData($I, 'validation') as $data) {
            $this->dataComment($I, $data);
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']);
            $I->seeResponseResultIsUnprocessableEntity();
        }
    }
}
