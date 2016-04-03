<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document\Merge\Rule;

use Codeception\TestCase\Test;
use common\components\xml\Document\Merge\Rule\IsNodeElement;

/**
 * Class IsNodeElementTest
 *
 * @see \common\components\xml\Document\Merge\Rule\IsNodeElement
 */
class IsNodeElementTest extends Test
{
    public function testValidate()
    {
        $rule = new IsNodeElement();

        $document = new \DOMDocument();

        $leftNode = $document->createElement('test');
        $rightNode = $document->createElement('test');

        self::assertTrue($rule->validate($leftNode, $rightNode));
    }

    public function testValidateFail()
    {
        $rule = new IsNodeElement();

        $document = new \DOMDocument();

        $leftNode = $document->createElement('bad');
        $rightNode = $document->createTextNode('test');

        self::assertFalse($rule->validate($leftNode, $rightNode));
    }
}
