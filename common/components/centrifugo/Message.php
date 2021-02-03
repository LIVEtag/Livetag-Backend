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
    //StreamSession
    const ACTION_STREAM_SESSION_CREATE = 'streamSessionCreate';
    const ACTION_STREAM_SESSION_UPDATE = 'streamSessionUpdate';
    const ACTION_STREAM_SESSION_END_SOON = 'streamSessionEndSoon';
    //Product
    const ACTION_PRODUCT_CREATE = 'productCreate';
    const ACTION_PRODUCT_UPDATE = 'productUpdate';
    const ACTION_PRODUCT_DELETE = 'productDelete';
    //Messages
    const ACTION_MESSAGE_CREATE = 'messageCreate';
    const ACTION_MESSAGE_UPDATE = 'messageUpdate';
    const ACTION_MESSAGE_DELETE = 'messageDelete';
    
    /**
     * Allowed actions by type
     */
    const ALLOWED_ACTIONS = [
        self::ACTION_STREAM_SESSION_CREATE,
        self::ACTION_STREAM_SESSION_UPDATE,
        self::ACTION_STREAM_SESSION_END_SOON,
        self::ACTION_PRODUCT_CREATE,
        self::ACTION_PRODUCT_UPDATE,
        self::ACTION_PRODUCT_DELETE,
        self::ACTION_MESSAGE_CREATE,
        self::ACTION_MESSAGE_UPDATE,
        self::ACTION_MESSAGE_DELETE,
    ];

    /** @var string */
    protected $action;

    /** @var array */
    protected $data;

    /**
     * @param string $action
     * @param array $data
     * @param array $config
     * @throws LogicException
     */
    public function __construct($action, array $data, $config = [])
    {
        if (!in_array($action, self::ALLOWED_ACTIONS)) {
            throw new LogicException('Not allowed action.');
        }
        $this->action = $action;
        $this->data = $data;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return [
            'action' => $this->action,
            'data' => $this->data,
        ];
    }
}
