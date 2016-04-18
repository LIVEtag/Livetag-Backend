<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger\Command;

use common\components\xml\Document\Node\Merger\CommandInterface;

/**
 * Class ReplaceContent
 */
class ReplaceContent implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function execute(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        if ($leftNode->hasChildNodes()) {
            $this->removeChildNodes($leftNode);
        }

        foreach ($this->collectNodes($rightNode) as $child) {
            $child = $leftNode->ownerDocument->importNode($child, true);
            $leftNode->appendChild($child);
        }
    }

    /**
     * @param \DOMNode $node
     * @return \DOMNode[]
     */
    private function collectNodes(\DOMNode $node)
    {
        $childNodes = [];
        /** @var \DOMNode $child */
        foreach ($node->childNodes as $child) {
            switch ($child->nodeType) {
                case XML_CDATA_SECTION_NODE:
                    // no break
                case XML_TEXT_NODE:
                    $childNodes[] = $node;
                    break;
            }
        }

        return $childNodes;
    }

    /**
     * @param \DOMNode $node
     */
    private function removeChildNodes(\DOMNode $node)
    {
        foreach ($this->collectNodes($node) as $child) {
            $node->removeChild($child);
        }
    }
}
