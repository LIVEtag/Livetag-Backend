<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node;

/**
 * Interface ResolverInterface
 */
interface ResolverInterface
{
    /**
     * Resolve rules of merge for merging current nodes
     *
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     * @return MergerInterface
     */
    public function resolve(\DOMNode $leftNode, \DOMNode $rightNode);
}
