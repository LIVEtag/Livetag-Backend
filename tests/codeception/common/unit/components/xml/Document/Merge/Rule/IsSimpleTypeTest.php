<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document\Merge\Rule;

use Codeception\TestCase\Test;
use common\components\xml\Document\Merge\Rule\IsSimpleType;

/**
 * Class IsSimpleTypeTest
 *
 * @see \common\components\xml\Document\Merge\Rule\IsSimpleType
 */
class IsSimpleTypeTest extends Test
{
    public function testValidate()
    {
        $rule = new IsSimpleType();

        $document = new \DOMDocument();
        $childNode = $document->createTextNode('child content');

        $leftNode = $document->createElement('test');
        $leftNode->appendChild(clone $childNode);

        $rightNode = $document->createElement('test');
        $rightNode->appendChild(clone $childNode);

        self::assertTrue($rule->validate($leftNode, $rightNode));
    }

    public function testValidateFail()
    {
        $rule = new IsSimpleType();

        $document = new \DOMDocument();
        $childNode = $document->createElement('child');

        $leftNode = $document->createElement('test');
        $leftNode->appendChild(clone $childNode);

        $rightNode = $document->createElement('test');
        $rightNode->appendChild(clone $childNode);

        self::assertFalse($rule->validate($leftNode, $rightNode));
    }

    public function testValidateMixedFail()
    {
        $rule = new IsSimpleType();

        $document = new \DOMDocument();
        $childNode = $document->createTextNode('child content');

        $leftNode = $document->createElement('test');
        $leftNode->appendChild(clone $childNode);

        $rightNode = $document->createElement('test');
        $rightNode->appendChild(clone $childNode);

        $childNode = $document->createElement('child');

        $leftNode->appendChild(clone $childNode);

        $rightNode->appendChild(clone $childNode);

        self::assertFalse($rule->validate($leftNode, $rightNode));
    }
}
