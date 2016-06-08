<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger\Command;

use common\components\xml\Document\Node\Merger\CommandInterface;
use common\components\xml\Document\Node\Merger\ContextValidator;

/**
 * Class ReplaceAttribute
 */
class ReplaceAttribute implements CommandInterface
{
    /**
     * @var ContextValidator
     */
    private $contextValidator;

    /**
     * Constructor
     *
     * @param ContextValidator $contextValidator
     */
    public function __construct(ContextValidator $contextValidator)
    {
        $this->contextValidator = $contextValidator;
    }

    /**
     * @inheritdoc
     */
    public function execute(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        $this->contextValidator->assertElementType($leftNode, $rightNode);

        if (!$rightNode->hasAttributes()) {
            return;
        }

        /**
         * @var \DOMElement $leftNode
         * @var \DOMElement $rightNode
         * @var \DOMAttr $attribute
         */
        foreach ($rightNode->attributes as $attribute)
        {
            $leftNode->setAttribute($attribute->name, $attribute->value);
        }
    }
}
