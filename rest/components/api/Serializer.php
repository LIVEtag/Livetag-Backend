<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api;

use yii\rest\Serializer as BaseSerializer;
use rest\components\validation\ErrorMessage;
use rest\components\validation\ErrorListInterface;

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
    public function serialize($data)
    {
        $data = parent::serialize($data);

        $dataResult = [
            'code' => $this->response->getStatusCode(),
            'status' => $this->response->getIsSuccessful() ? 'success' : 'error',
            'result' => $data,
        ];

        if (is_array($data) && isset($data[$this->collectionEnvelope])) {
            $dataResult['result'] = $data[$this->collectionEnvelope];
        }

        return $dataResult;
    }

    /**
     * @inheritdoc
     */
    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        $result = [];
        foreach ($model->getFirstErrors() as $attribute => $message) {
            if ($message instanceof ErrorMessage) {
                $code = $message->getCode();
                $params = $message->getParams();
            } else {
                $code = ErrorListInterface::ERR_BASIC;
                $params = [];
            }
            $serializedParams = [];
            foreach ($params as $name => $value) {
                $serializedParams[] = [
                    'name' => $name,
                    'value' => (string) $value,
                ];
            }
            $result[] = [
                'field' => $attribute,
                'message' => (string) $message,
                'code' => $code,
                'params' => $serializedParams
            ];
        }
        return $result;
    }
}
