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

        $newText = new \DOMText($rightNode->textContent);
        $leftNode->appendChild($newText);
    }

    /**
     * @param \DOMNode $node
     */
    private function removeChildNodes(\DOMNode $node)
    {
        $childNodes = [];
        foreach ($node->childNodes as $node) {
            $childNodes[] = $node;
        }
        foreach ($childNodes as $node) {
            $node->removeChild($node);
        }
    }
}
