<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger\Rule;

use common\components\xml\Document\Node\Merger\ContextValidator;
use common\components\xml\Document\Node\Merger\RuleInterface;

/**
 * Class EqualAttribute
 */
class EqualAttribute implements RuleInterface
{
    /**
     * @var ContextValidator
     */
    private $contextValidator;

    /**
     * @var string[]
     */
    private $attributes;

    /**
     * Constructor
     *
     * @param string[] $attributes
     * @param ContextValidator $contextValidator
     */
    public function __construct(
        array $attributes,
        ContextValidator $contextValidator
    ) {
        $this->contextValidator = $contextValidator;
        $this->attributes = $attributes;
    }

    /**
     * @inheritdoc
     */
    public function validate(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        $this->contextValidator->assertElementType($leftNode, $rightNode);

        /** @var \DOMElement $leftNode */
        /** @var \DOMElement $rightNode */
        return $this->hasAttributes($leftNode)
            && $this->hasAttributes($rightNode)
            && $this->checkEqualAttributes($leftNode, $rightNode);
    }

    /**
     * @param \DOMElement $node
     * @return bool
     */
    private function hasAttributes(\DOMElement $node)
    {
        foreach ($this->attributes as $attribute) {
            if (!$node->hasAttribute($attribute)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check the attributes of equality
     *
     * @param \DOMElement $leftNode
     * @param \DOMElement $rightNode
     * @return bool
     */
    private function checkEqualAttributes(\DOMElement $leftNode, \DOMElement $rightNode)
    {
        foreach ($this->attributes as $attribute) {
            if ($leftNode->getAttribute($attribute) !== $rightNode->getAttribute($attribute)) {
                return false;
            }
        }

        return true;
    }
}
