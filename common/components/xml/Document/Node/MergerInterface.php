<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node;

/**
 * Interface MergerInterface
 */
interface MergerInterface
{
    /**
     * Merger document node
     *
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     * @return \DOMNode
     */
    public function merge(\DOMNode $leftNode, \DOMNode $rightNode);
}
