<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document\Merge\Rule;

use Codeception\TestCase\Test;
use common\components\xml\Document\Merge\ContextValidator;
use common\components\xml\Document\Merge\Rule\EqualAttribute;

/**
 * Class EqualAttributeTest
 *
 * @see \common\components\xml\Document\Merge\Rule\EqualAttribute
 */
class EqualAttributeTest extends Test
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

    /**
     * @param array $attributes
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $attributes)
    {
        $rule = new EqualAttribute(array_keys($attributes), $this->contextValidator);

        $leftNode = $this->createNode($attributes);
        $rightNode = $this->createNode($attributes);

        self::assertTrue($rule->validate($leftNode, $rightNode));
    }

    /**
     * @param array $attributes
     * @dataProvider validateDataProvider
     */
    public function testValidateFail(array $attributes)
    {
        $rule = new EqualAttribute($attributes, $this->contextValidator);

        $leftNode = $this->createNode($attributes);
        $rightNode = $this->createNode($attributes);

        self::assertFalse($rule->validate($leftNode, $rightNode));
    }

    /**
     * @return array
     */
    public function validateDataProvider()
    {

        return [
            [
                'attributes' => $this->generateAttributes(mt_rand(2, 10))
            ],
            [
                'attributes' => $this->generateAttributes(mt_rand(2, 10))
            ],
            [
                'attributes' => $this->generateAttributes(mt_rand(2, 10))
            ],
        ];
    }

    /**
     * @param array $attributes
     * @return \DOMElement
     */
    private function createNode(array $attributes)
    {
        $document = new \DOMDocument();
        $node = $document->createElement('test');
        foreach ($attributes as $name => $value) {
            $node->setAttribute($name, $value);
        }

        return $node;
    }

    /**
     * @param int $count
     * @return array
     */
    private function generateAttributes($count)
    {
        $count = abs($count);
        $attributes = [];
        $pool = 'abcdefghijklmnopqrstuvwxyz';

        while ($count--) {
            $name = substr(str_shuffle(str_repeat($pool, 5)), 0, 6);
            $value = substr(str_shuffle(str_repeat($pool, 5)), 0, 6);
            $attributes[$name] = $value;
        }

        return $attributes;
    }
}
