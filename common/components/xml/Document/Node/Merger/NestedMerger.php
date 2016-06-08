<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node\Merger;

use common\components\xml\Document\Node\MergerInterface;
use common\components\xml\Document\Node\Resolver;

/**
 * Class NestedMerger
 */
class NestedMerger implements MergerInterface
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * Constructor
     *
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @inheritdoc
     */
    public function merge(\DOMNode $leftNode, \DOMNode $rightNode)
    {
        return $this->resolver->resolve($leftNode, $rightNode)
            ->merge($leftNode, $rightNode);
    }
}
