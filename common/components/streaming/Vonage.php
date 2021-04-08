<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\streaming;

use OpenTok\ArchiveMode;
use OpenTok\Exception\DomainException;
use OpenTok\MediaMode;
use OpenTok\OpenTok;
use OpenTok\OutputMode;
use yii\base\Component;
use yii\web\UnprocessableEntityHttpException;

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
            'archiveMode' => ArchiveMode::MANUAL,
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

    /**
     * Start archiving
     *
     * @param string $sessionId
     *
     * @return void
     */
    public function startArchiving($sessionId)
    {
        try {
            return $this->opentok->startArchive($sessionId, [
                'outputMode' => OutputMode::INDIVIDUAL, // default: OutputMode::COMPOSED
                //'resolution' => '1280x720',// default: '640x480'
            ]);
        } catch (DomainException $e) {
            $this->processClientException($e);
        }
    }

    /**
     * Stop archiving
     *
     * @param string $archiveId
     */
    public function stopArchive($archiveId)
    {
        try {
            return $this->opentok->stopArchive($archiveId);
        } catch (DomainException $e) {
            $this->processClientException($e);
        }
    }

    /**
     * Process Client Exception and re-throw correct message
     * @param DomainException $e
     * @throws UnprocessableEntityHttpException
     */
    protected function processClientException(DomainException $e)
    {
        //(string) Client error: `POST https://api.opentok.com/v2/project/47067894/archive` resulted in a `404 Not Found` response:
        //{ "message" : "Not found. No clients are actively connected to the OpenTok session." }
        $error = $e->getMessage();
        if (preg_match_all('/"([^"]+)"/', $error, $data)) {
            $last = end($data);
            throw new UnprocessableEntityHttpException(array_pop($last));
        } elseif ($error) {
            throw new UnprocessableEntityHttpException($error);
        }
        throw $e; //just in case
    }
}
