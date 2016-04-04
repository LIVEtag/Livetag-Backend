<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document\Node\Merger\Rule;

use Codeception\TestCase\Test;
use common\components\xml\Document\Node\Merger\Rule\EqualNodeName;

/**
 * Class EqualNodeNameTest
 *
 * @see \common\components\xml\Document\Node\Merger\Rule\EqualNodeName
 */
class EqualNodeNameTest extends Test
{
    public function testValidate()
    {
        $rule = new EqualNodeName();

        $document = new \DOMDocument();

        $leftNode = $document->createElement('test');
        $rightNode = $document->createElement('test');

        self::assertTrue($rule->validate($leftNode, $rightNode));
    }

    public function testValidateFail()
    {
        $rule = new EqualNodeName();

        $document = new \DOMDocument();

        $leftNode = $document->createElement('bad');
        $rightNode = $document->createElement('test');

        self::assertFalse($rule->validate($leftNode, $rightNode));
    }
}
