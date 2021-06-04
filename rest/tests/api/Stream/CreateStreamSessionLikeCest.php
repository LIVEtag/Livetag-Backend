<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests\api\Stream;

use rest\tests\AccessTestTrait;
use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;

class CreateStreamSessionLikeCest extends ActionCest
{
    use ProviderDataTrait;
    use AccessTestTrait;

    /** @var int */
    protected $streamSessionId;

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
        return '/stream-session/' . $this->streamSessionId . '/like';
    }

    /**
     * @param ApiTester $I
     */
    public function create(ApiTester $I)
    {
        foreach ($this->getProviderData($I, 'create') as $data) {
            $this->dataComment($I, $data);
            $I->amLoggedInApiAs($data['userId']);
            $this->streamSessionId = $data['streamSessionId'];
            $I->send(
                $this->getMethod(),
                $this->getUrl($I)
            );
            $I->seeResponseResultIsCreated();
        }
    }
}
