<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document;

use common\components\xml\Document\Node\ResolverInterface;

/**
 * Class Merger
 */
class Merger implements MergerInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var \DOMDocument
     */
    private $document;

    /**
     * Constructor
     *
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @inheritdoc
     */
    public function merge(\DOMDocument $document)
    {
        if ($this->document === null) {
            $this->document = $document;
            return;
        }
    }

    /**
     * @inheritdoc
     */
    public function getDocument()
    {
        return clone $this->document;
    }
}
