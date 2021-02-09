<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\centrifugo\channels;

use common\components\centrifugo\ChannelInterface;
use yii\base\BaseObject;

/**
 * Class SessionChannel
 * @package common\components\centrifugo
 */
class SessionChannel extends BaseObject implements ChannelInterface
{
    /**
     * Channel preffix
     */
    const PREFIX = 'session';

    /** @var string */
    protected $sessionId;
    
    /**
     * PostChannel constructor.
     * @param int $sessionId
     * @param array $config $array
     */
    public function __construct(int $sessionId, $config = [])
    {
        $this->sessionId = $sessionId;
        parent::__construct($config);
    }

    /**
     * Get name of channel
     * @return string
     */
    public function getName(): string
    {
        return self::PREFIX . '_' . $this->sessionId;
    }
}
