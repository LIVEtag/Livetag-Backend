<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Merge\Rule;

use common\components\xml\Document\Merge\RuleInterface;

/**
 * Class IsNodeElement
 */
class IsNodeElement implements RuleInterface
{
    /**
     * @inheritdoc
     */
    public function validate(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        return $leftNode->nodeType === XML_ELEMENT_NODE
            && $rightNode->nodeType === XML_ELEMENT_NODE;
    }
}
