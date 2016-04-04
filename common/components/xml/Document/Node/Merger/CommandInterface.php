<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger;

/**
 * Interface CommandInterface
 */
interface CommandInterface
{
    /**
     * Execute command
     *
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     */
    public function execute(\DOMNode $leftNode, \DOMNode $rightNode);
}
