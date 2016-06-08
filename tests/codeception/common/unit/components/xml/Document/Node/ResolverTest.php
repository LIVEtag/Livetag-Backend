<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document\Node;

use Codeception\TestCase\Test;
use common\components\xml\Document\Node\Merger\ContextValidator;
use common\components\xml\Document\Node\Merger\Rule\EqualAttribute;
use common\components\xml\Document\Node\Merger\Rule\EqualNodeName;
use common\components\xml\Document\Node\Merger\Rule\IsComplexType;
use common\components\xml\Document\Node\Merger\Rule\IsNodeElement;
use common\components\xml\Document\Node\Merger\Rule\IsSimpleType;
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
     * @param string $expected
     * @param array $config
     * @param \DOMNode $leftNode
     * @param \DOMNode $rightNode
     *
     * @dataProvider resolveDataProvider
     */
    public function testResolve($expected, array $config, \DOMNode $leftNode, \DOMNode $rightNode)
    {
        $resolver = new Resolver($config);

        $merger = $resolver->resolve($leftNode, $rightNode);

        self::assertEquals($expected, $merger);
    }

    /**
     * @return array
     */
    public function resolveDataProvider()
    {
        $contextValidator = new ContextValidator();
        $document = new \DOMDocument();
        $document->load(__DIR__ . '/src/doc-1.xml');
        $node = $document->getElementsByTagName('test')->item(0);
        $nodeText = $document->getElementsByTagName('text')->item(0);

        return [
            [
                'expected' => MergerInterface::class . 'Complex',
                'config' => [
                    Resolver::RULES => [
                        'complex' => [
                            Resolver::RULE => $this->create(IsNodeElement::class),
                            Resolver::NEXT => [
                                Resolver::RULE => $this->create(IsComplexType::class, [$contextValidator]),
                                Resolver::NEXT => [
                                    Resolver::RULE => $this->create(EqualNodeName::class),
                                    Resolver::NEXT => [
                                        Resolver::RULE => $this->create(
                                            EqualAttribute::class,
                                            [['name'], $contextValidator]
                                        ),
                                        Resolver::MERGER => MergerInterface::class . 'Complex'
                                    ]
                                ]
                            ]
                        ],
                        'simple' => [
                            Resolver::RULE => $this->create(IsNodeElement::class),
                            Resolver::NEXT => [
                                Resolver::RULE => $this->create(IsSimpleType::class),
                                Resolver::NEXT => [
                                    Resolver::RULE => $this->create(EqualNodeName::class),
                                    Resolver::NEXT => [
                                        Resolver::RULE => $this->create(
                                            EqualAttribute::class,
                                            [['name'], $contextValidator]
                                        ),
                                        Resolver::MERGER => MergerInterface::class
                                    ]
                                ]
                            ]
                        ]
                    ],
                    Resolver::DEFAULT_MERGER => MergerInterface::class . 'Default'
                ],
                'leftNode' => $node,
                'rightNode' => $node,
            ],
            [
                'expected' => MergerInterface::class . 'Simple',
                'config' => [
                    Resolver::RULES => [
                        'complex' => [
                            Resolver::RULE => $this->create(IsNodeElement::class),
                            Resolver::NEXT => [
                                Resolver::RULE => $this->create(IsComplexType::class, [$contextValidator]),
                                Resolver::NEXT => [
                                    Resolver::RULE => $this->create(EqualNodeName::class),
                                    Resolver::NEXT => [
                                        Resolver::RULE => $this->create(
                                            EqualAttribute::class,
                                            [['name'], $contextValidator]
                                        ),
                                        Resolver::MERGER => MergerInterface::class
                                    ]
                                ]
                            ]
                        ],
                        'simple' => [
                            Resolver::RULE => $this->create(IsNodeElement::class),
                            Resolver::NEXT => [
                                Resolver::RULE => $this->create(IsSimpleType::class),
                                Resolver::NEXT => [
                                    Resolver::RULE => $this->create(EqualNodeName::class),
                                    Resolver::NEXT => [
                                        Resolver::RULE => $this->create(
                                            EqualAttribute::class,
                                            [['name'], $contextValidator]
                                        ),
                                        Resolver::MERGER => MergerInterface::class . 'Simple'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    Resolver::DEFAULT_MERGER => MergerInterface::class . 'Default'
                ],
                'leftNode' => $nodeText,
                'rightNode' => $nodeText,
            ]
        ];
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return object
     */
    private function create($className, array $arguments = [])
    {
        $class = new \ReflectionClass($className);

        return $class->newInstanceArgs($arguments);
    }
}
