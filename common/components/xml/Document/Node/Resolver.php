<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document\Node;

/**
 * Class Resolver
 */
class Resolver implements ResolverInterface
{
    const RULES = 'rules';

    const DEFAULT_MERGER = 'default';

    /**
     * @var array
     */
    private $config;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function resolve(\DOMNode $leftNode, \DOMNode $rightNode)
    {

    }
}
