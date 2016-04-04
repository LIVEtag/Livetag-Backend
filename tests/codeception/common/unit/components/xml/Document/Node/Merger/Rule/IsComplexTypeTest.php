<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document\Node\Merger\Rule;

use Codeception\TestCase\Test;
use common\components\xml\Document\Node\Merger\ContextValidator;
use common\components\xml\Document\Node\Merger\Rule\IsComplexType;

/**
 * Class IsComplexTypeTest
 *
 * @see \common\components\xml\Document\Node\Merger\Rule\IsComplexType
 */
class IsComplexTypeTest extends Test
{
    /**
     * @var ContextValidator
     */
    private $contextValidator;

    /**
     * @inheritdoc
     */
    protected function _before()
    {
        parent::_before();
        $this->contextValidator = new ContextValidator();
    }

    public function testValidate()
    {
        $rule = new IsComplexType($this->contextValidator);

        $document = new \DOMDocument();
        $childNode = $document->createElement('child');

        $leftNode = $document->createElement('test');
        $leftNode->appendChild(clone $childNode);

        $rightNode = $document->createElement('test');
        $rightNode->appendChild(clone $childNode);

        self::assertTrue($rule->validate($leftNode, $rightNode));
    }

    public function testValidateMixed()
    {
        $rule = new IsComplexType($this->contextValidator);

        $document = new \DOMDocument();
        $childNode = $document->createTextNode('child content');

        $leftNode = $document->createElement('test');
        $leftNode->appendChild(clone $childNode);

        $rightNode = $document->createElement('test');
        $rightNode->appendChild(clone $childNode);

        $childNode = $document->createElement('child');

        $leftNode->appendChild(clone $childNode);

        $rightNode->appendChild(clone $childNode);

        self::assertTrue($rule->validate($leftNode, $rightNode));
    }

    public function testValidateFail()
    {
        $rule = new IsComplexType($this->contextValidator);

        $document = new \DOMDocument();
        $childNode = $document->createTextNode('child content');

        $leftNode = $document->createElement('test');
        $leftNode->appendChild(clone $childNode);

        $rightNode = $document->createElement('test');
        $rightNode->appendChild(clone $childNode);

        self::assertFalse($rule->validate($leftNode, $rightNode));
    }
}
