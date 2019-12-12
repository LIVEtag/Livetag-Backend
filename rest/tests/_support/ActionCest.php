<?php
/**
 * Copyright Â© 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use Codeception\Util\HttpCode;

abstract class ActionCest
{
    use BaseFixtureTrait;

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PUT = 'PUT';

    /**
     * Base method for this action
     * @var string
     * @return string
     */
    abstract protected function getMethod(): string;

    /**
     * Base url for this action
     * @param ApiTester $I
     * @return string
     */
    abstract protected function getUrl(ApiTester $I): string;

    /**
     * @param ApiTester $I
     */
    public function options(ApiTester $I)
    {
        $I->sendOPTIONS($this->getUrl($I));
        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
