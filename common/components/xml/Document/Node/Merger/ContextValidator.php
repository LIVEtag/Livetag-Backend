<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger;

/**
 * Class ContextValidator
 */
class ContextValidator
{
    /**
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     * @throws ContextException
     */
    public function assertElementType(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        if ($leftNode->nodeType !== XML_ELEMENT_NODE
            || $rightNode->nodeType !== XML_ELEMENT_NODE) {
            throw new ContextException('Left and right nodes must be "XML_ELEMENT_NODE" type.');
        }
    }

    /**
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     * @throws ContextException
     */
    public function assertTextType(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        if ($leftNode->nodeType !== XML_TEXT_NODE
            || $rightNode->nodeType !== XML_TEXT_NODE) {
            throw new ContextException('Left and right nodes must be "XML_TEXT_NODE" type.');
        }
    }
}
