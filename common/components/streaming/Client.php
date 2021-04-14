<?php

namespace common\components\streaming;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use OpenTok\Exception\AuthenticationException;
use OpenTok\Exception\DomainException;
use OpenTok\Exception\UnexpectedValueException;
use OpenTok\Util\Client as BaseCLient;

/**
 * @inheritdoc
 */
class Client extends BaseCLient
{

    /**
     * Get archive by session id
     * @param string $sessionId
     * @return array
     */
    public function getArchiveBySessionId($sessionId)
    {
        $request = new Request('GET', '/v2/project/' . $this->apiKey . '/archive?sessionId=' . $sessionId);
        try {
            $response = $this->client->send($request);
            $archiveJson = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $this->handleException($e);
            return;
        }
        return $archiveJson;
    }

    /**
     * Copy from original class with no changes (it's private :()
     * @param type $e
     * @throws AuthenticationException
     * @throws DomainException
     * @throws UnexpectedValueException
     * @throws \Exception
     */
    private function handleException($e)
    {
        // TODO: test coverage
        if ($e instanceof ClientException) {
            // will catch all 4xx errors
            if ($e->getResponse()->getStatusCode() == 403) {
                throw new AuthenticationException(
                    $this->apiKey,
                    $this->apiSecret,
                    null,
                    $e
                );
            } else {
                throw new DomainException(
                    'The OpenTok API request failed: ' . json_decode($e->getResponse()->getBody(true))->message,
                    null,
                    $e
                );
            }
        } else if ($e instanceof ServerException) {
            // will catch all 5xx errors
            throw new UnexpectedValueException(
                'The OpenTok API server responded with an error: ' . json_decode($e->getResponse()->getBody(true))->message,
                null,
                $e
            );
        } else {
            // TODO: check if this works because Exception is an interface not a class
            throw new Exception('An unexpected error occurred');
        }
    }
}
