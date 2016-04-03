<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\xml\Document;

/**
 * Interface MergerInterface
 */
interface MergerInterface
{
    /**
     * Merge document
     *
     * @param \DOMDocument $document
     */
    public function merge(\DOMDocument $document);

    /**
     * @return \DOMDocument
     */
    public function getDocument();
}
