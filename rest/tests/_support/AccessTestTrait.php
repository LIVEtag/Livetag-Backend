<?php
/**
 * Copyright Â© 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use common\fixtures\UserFixture;
use common\models\User;

trait AccessTestTrait
{
    /**
     * @param ApiTester $I
     */
    public function guestAccess(ApiTester $I)
    {
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsUnauthorized();
    }

    /**
     * @param ApiTester $I
     */
    public function deletedAccess(ApiTester $I)
    {
        /** @var User $email */
        $email = $I->grabFixture('users', UserFixture::DELETED);
        $I->sendPOST('/user/login', [
            'email' => $email->email,
            'password' => UserFixture::DEFAULT_PASSWORD,
        ]);
        $I->send($this->getMethod(), $this->getUrl($I));
        $I->seeResponseResultIsUnauthorized();
    }
}
