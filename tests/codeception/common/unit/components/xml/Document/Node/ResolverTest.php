<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document\Node;

use Codeception\TestCase\Test;
use common\components\xml\Document\Merge\Rule\EqualAttribute;
use common\components\xml\Document\Merge\Rule\EqualNodeName;
use common\components\xml\Document\Merge\Rule\IsComplexType;
use common\components\xml\Document\Merge\Rule\IsNodeElement;
use common\components\xml\Document\Merge\Rule\IsSimpleType;
use common\components\xml\Document\Node\MergerInterface;
use common\components\xml\Document\Node\Resolver;

/**
 * Class ResolverTest
 *
 * @see \common\components\xml\Document\Node\Resolver
 */
class ResolverTest extends Test
{
    /**
     * @param array $config
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     *
     * @dataProvider resolveDataProvider
     */
    public function testResolve(array $config, \DOMNode $leftNode, \DOMNode $rightNode)
    {
        $resolver = new Resolver($config);

        $merger = $resolver->resolve($leftNode, $rightNode);

        self::assertInstanceOf(MergerInterface::class, $merger);
    }

    /**
     * @return array
     */
    public function resolveDataProvider()
    {
        $document = new \DOMDocument();
        $document->load(__DIR__ . '/src/doc-1.xml');
        $node = $document->getElementsByTagName('test')->item(0);

        return [
            [
                'config' => [
                    Resolver::RULES => [
                        'complex' => [
                            'rule' => IsNodeElement::class,
                            'next' => [
                                'rule' => IsComplexType::class,
                                'next' => [
                                    'rule' => EqualNodeName::class,
                                    'next' => [
                                        'rule' => EqualAttribute::class,
                                        'merger' => MergerInterface::class
                                    ]
                                ]
                            ]
                        ],
                        'simple' => [
                            'rule' => IsNodeElement::class,
                            'next' => [
                                'rule' => IsSimpleType::class,
                                'next' => [
                                    'rule' => EqualNodeName::class,
                                    'next' => [
                                        'rule' => EqualAttribute::class,
                                        'merger' => MergerInterface::class
                                    ]
                                ]
                            ]
                        ]
                    ],
                    Resolver::DEFAULT_MERGER => MergerInterface::class
                ],
                'leftNode' => $node,
                'rightNode' => $node,
            ]
        ];
    }
}
