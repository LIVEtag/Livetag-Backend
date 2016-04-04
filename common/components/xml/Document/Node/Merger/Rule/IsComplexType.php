<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger\Rule;

use common\components\xml\Document\Node\Merger\ContextValidator;
use common\components\xml\Document\Node\Merger\RuleInterface;

/**
 * Class IsComplexType
 */
class IsComplexType implements RuleInterface
{
    /**
     * @var ContextValidator
     */
    private $contextValidator;

    /**
     * Constructor
     *
     * @param ContextValidator $contextValidator
     */
    public function __construct(ContextValidator $contextValidator)
    {
        $this->contextValidator = $contextValidator;
    }

    /**
     * @inheritdoc
     */
    public function validate(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        $this->contextValidator->assertElementType($leftNode, $rightNode);

        /** @var \DOMElement $leftNode */
        /** @var \DOMElement $rightNode */
        return $leftNode->hasChildNodes()
            && $rightNode->hasChildNodes()
            && $this->checkType($leftNode)
            && $this->checkType($rightNode);
    }

    /**
     * @param \DOMElement $node
     * @return bool
     */
    private function checkType(\DOMElement $node)
    {
        /** @var \DOMNode $childNode */
        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                return true;
            }
        }

        return false;
    }
}
