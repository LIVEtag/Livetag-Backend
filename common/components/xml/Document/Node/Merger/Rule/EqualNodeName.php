<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger\Rule;

use common\components\xml\Document\Node\Merger\RuleInterface;

/**
 * Class EqualNodeName
 */
class EqualNodeName implements RuleInterface
{
    /**
     * @inheritdoc
     */
    public function validate(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        return $leftNode->nodeName === $rightNode->nodeName;
    }
}