<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\streaming;

use OpenTok\MediaMode;
use OpenTok\OpenTok;
use yii\base\Component;

/**
 * Vonage Component
 */
class Vonage extends Component
{
    /** @var string */
    public $apiKey;

    /** @var string */
    public $apiSecret;

    /** @var OpenTok */
    private $opentok;

    /**
     * Component initialization
     */
    public function init()
    {
        $this->opentok = new OpenTok($this->apiKey, $this->apiSecret);
    }

    /**
     * Create session
     *
     * @return string
     */
    public function createSession(): string
    {
        $session = $this->opentok->createSession([
            'mediaMode' => MediaMode::ROUTED,
        ]);
        return $session->getSessionId();
    }

    /**
     * Generate token for session
     *
     * @param string $sessionId
     * @param array $options
     *
     * @return string
     */
    public function getToken($sessionId, $options): string
    {
        return $this->opentok->generateToken($sessionId, $options);
    }
}
