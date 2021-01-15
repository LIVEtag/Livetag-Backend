<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\centrifugo;

use LogicException;
use yii\base\BaseObject;

/**
 * Class Message
 * @package common\components\centrifugo
 * @property-read array $body
 */
class Message extends BaseObject implements MessageInterface
{
    const TYPE_STREAM_SESSION = 'streamSession';
    const TYPE_PRODUCT = 'product';
    const TYPE_MESSAGE = 'message';

    /**
     * All allowed entities types
     */
    const ALLOWED_TYPES = [
        self::TYPE_STREAM_SESSION,
        self::TYPE_PRODUCT,
        self::TYPE_MESSAGE,
    ];

    /** @var string */
    protected $action;

    /** @var string */
    protected $type;

    /** @var array */
    protected $data;

    /**
     * Data (model) setter
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Type setter
     * @param string $type
     * @throws LogicException
     */
    public function setType(string $type)
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new LogicException('Not allowed type.');
        }

        $this->type = $type;
    }

    /**
     * Action setter
     * @param string $action
     * @throws LogicException
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return [
            'action' => $this->action,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}
