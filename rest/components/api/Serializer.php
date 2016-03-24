<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api;

use yii\rest\Serializer as BaseSerializer;

/**
 * Class Serializer
 */
class Serializer extends BaseSerializer
{
    /**
     * @inheritdoc
     */
    public $collectionEnvelope = 'items';
    
    /**
     * @inheritdoc
     */
    public function serialize($data) {
        $_data = parent::serialize($data);
        
        if (!is_array($_data)) {
            return [];
        }
        
        /**
         * Ignore meta data for collection in response
         */
        if (isset($_data[$this->collectionEnvelope])) {
            return $_data[$this->collectionEnvelope];
        }
        
        return $_data;
    }
}
