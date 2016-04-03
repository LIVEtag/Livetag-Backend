<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Merge;

/**
 * Interface RuleInterface
 */
interface RuleInterface
{
    /**
     * Validate nodes for merging
     *
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     * @return bool
     */
    public function validate(\DOMNode $leftNode, \DOMNode $rightNode);
}
