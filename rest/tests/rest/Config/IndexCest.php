<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rests\tests\rest\Config;

use Codeception\Util\HttpCode;
use rest\tests\RestTester;

/**
 * Class IndexCest
 * @group config
 */
class IndexCest
{
    public $url = '/config';

    /**
     * @param RestTester $I
     */
    public function checkResponseStructureIsCorrect(RestTester $I)
    {
        $I->sendGET($this->url);
        $I->seeResponseStructure200();
        $I->seeResponseMatchesJsonType([
            'version' => [
                'major' => 'integer',
                'minor' => 'integer',
                'patch' => 'integer',
                'commit' => 'string|null'
            ],
            'parameters' => 'array',
            'errors' => 'array',
        ], '$.result');
    }
}
