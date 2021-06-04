<?php

namespace rest\tests\api\Analytics;

use rest\tests\ActionCest;
use rest\tests\ApiTester;
use rest\tests\ProviderDataTrait;

class StreamSessionProductEventCest extends ActionCest
{
    use ProviderDataTrait;
    /** @var int */
    protected $streamSessionId;

    /** @var int */
    protected $productId;

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
        return '/stream-session/' . $this->streamSessionId . '/product/' . $this->productId . '/event';
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
            $this->productId = $data['productId'];
            $I->send($this->getMethod(), $this->getUrl($I), $data['request']['data']);
            $I->seeResponseResultIsNoContent();
        }
    }
}
