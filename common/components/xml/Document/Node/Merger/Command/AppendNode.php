<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger\Command;

use common\components\xml\Document\Node\Merger\CommandInterface;

/**
 * Class AppendNode
 */
class AppendNode implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function execute(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        if (!$rightNode->hasChildNodes()) {
            return;
        }

        foreach ($this->collectNodes($rightNode) as $node) {
            $node = $leftNode->ownerDocument->importNode($node, true);
            $leftNode->appendChild($node);
        }
    }

    /**
     * @param \DOMNode $node
     * @return array
     */
    private function collectNodes(\DOMNode $node)
    {
        $childNodes = [];
        foreach ($node->childNodes as $node) {
            $childNodes[] = $node;
        }

        return $childNodes;
    }
}
