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
        $data = parent::serialize($data);

        $dataResult = [
            'code' => $this->response->getStatusCode(),
            'status' => $this->response->getIsSuccessful() ? 'success' : 'error',
            'result' => [],
        ];

        if (is_array($data) && isset($data[$this->collectionEnvelope])) {
            $dataResult['result'] = $data[$this->collectionEnvelope];
        } else {
            $dataResult['result'] = $data;
        }

        return $dataResult;
    }
}
