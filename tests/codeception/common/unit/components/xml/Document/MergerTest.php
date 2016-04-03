<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\common\unit\components\xml\Document;

use Codeception\TestCase\Test;
use common\components\xml\Document\Merger;
use common\components\xml\Document\Node\Resolver;
use common\components\xml\DocumentFactory;

/**
 * Class MergerTest
 *
 * @see \common\components\xml\Document\Merger
 */
class MergerTest extends Test
{
    /**
     * Run merge method
     *
     * @param array $config
     * @param \DOMDocument[] $documents
     * @param string $expected
     *
     * @dataProvider configDataProvider
     */
    public function testMerge(array $config, array $documents, $expected)
    {
        $resolver = new Resolver($config);
        $merger = new Merger($resolver);

        foreach ($documents as $document) {
            $merger->merge($document);
        }

        $actualDocument = $merger->getDocument();
        $actualDocument->preserveWhiteSpace = false;
        $actualDocument->formatOutput = true;

        self::assertEquals($expected, $merger->getDocument()->saveXML(null, LIBXML_NOEMPTYTAG));
    }

    /**
     * @return array
     */
    public function configDataProvider()
    {
        $documentFactory = new DocumentFactory();

        $document = $documentFactory->create(file_get_contents(__DIR__ . '/src/doc-1.xml'));
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        return [
            [
                'config' => [],
                'documents' => [
                    $documentFactory->create(file_get_contents(__DIR__ . '/src/doc-1/one.xml')),
                    $documentFactory->create(file_get_contents(__DIR__ . '/src/doc-1/two.xml')),
                ],
                'expected' => $document->saveXML(null, LIBXML_NOEMPTYTAG)
            ]
        ];
    }
}
