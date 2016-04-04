<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger\Rule;

use common\components\xml\Document\Node\Merger\RuleInterface;

/**
 * Class IsSimpleType
 */
class IsSimpleType implements RuleInterface
{
    /**
     * @inheritdoc
     */
    public function validate(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        return $this->isSimpleNode($leftNode) && $this->isSimpleNode($rightNode);
    }

    /**
     * @param \DOMNode $node
     * @return bool
     */
    private function isSimpleNode(\DOMNode $node)
    {
        /** @var \DOMNode $childNode */
        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                return false;
            }
        }

        return true;
    }
}
