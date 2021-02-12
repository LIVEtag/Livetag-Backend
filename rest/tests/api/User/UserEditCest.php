<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Shop;

use common\fixtures\UserFixture;
use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;

/**
 * @group User
 */
class UserEditCest extends ActionCest
{
    use ProviderDataTrait;

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return self::METHOD_PATCH;
    }

    /**
     * @param ApiTester $I
     * @return string
     */
    protected function getUrl(ApiTester $I): string
    {
        return '/user';
    }

    /**
     * @param ApiTester $I
     */
    public function successUserEdit(ApiTester $I)
    {
        $I->amLoggedInApiAs(UserFixture::SELLER_1);
        $I->send($this->getMethod(), $this->getUrl($I), [
            'name' => 'test string',
        ]);
        $I->seeResponseResultIsOk();
        $I->seeResponseMatchesJsonType($I->getUserEditResponse(), '$.result');
    }
}
