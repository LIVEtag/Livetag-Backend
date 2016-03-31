<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml;

/**
 * Class DocumentFactory
 */
class DocumentFactory
{
    /**
     * Document version
     */
    const VERSION = '1.0';

    /**
     * Document encoding
     */
    const ENCODING = 'UTF-8';

    /**
     * Create document
     *
     * @param string $content
     * @return \DOMDocument
     */
    public function create($content)
    {
        $document = new \DOMDocument(self::VERSION, self::ENCODING);
        $document->loadXML($content);

        return $document;
    }
}
