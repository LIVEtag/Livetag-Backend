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
    const MERGER = 'merger';

    const NEXT = 'next';

    const RULE = 'rule';

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
        foreach ($this->config[self::RULES] as $rules) {
            $merger = $this->validate($rules, $leftNode, $rightNode);
            if ($merger !== false) {
                return $merger;
            }
        }

        return $this->config[self::DEFAULT_MERGER];
    }

    /**
     * @param array $config
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     * @return MergerInterface|bool
     */
    private function validate(array $config, \DOMNode $leftNode, \DOMNode $rightNode)
    {
        if (isset($config[self::RULE]) && $config[self::RULE]->validate($leftNode, $rightNode)) {
            if (isset($config[self::NEXT])) {
                return $this->validate($config[self::NEXT], $leftNode, $rightNode);
            }
            if (isset($config[self::MERGER])) {
                return $config[self::MERGER];
            }
        }

        return false;
    }
}
