<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\components\centrifugo;

use common\helpers\LogHelper;
use phpcent\Client;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Class Centrifugo
 * @property Client $client
 */
class Centrifugo extends Component
{
    /**
     * @var string
     */
    public $secret;

    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $ws;

    /**
     * @var
     */
    public $apiKey;

    /**
     * @var Client
     */
    private $phpcentClient;

    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'centrifugo';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->secret) {
            throw new InvalidConfigException('Centrifugo Secret key is not provided.');
        }

        if (!$this->host) {
            throw new InvalidConfigException('Centrifugo host url is not provided.');
        }

        if (!$this->ws) {
            throw new InvalidConfigException('Centrifugo websocket url is not provided.');
        }

        if (!$this->apiKey) {
            throw new InvalidConfigException('Centrifugo apikey url is not provided.');
        }

        $this->phpcentClient = new Client($this->host, $this->apiKey, $this->secret);

        parent::init();
    }

    /**
     * client getter
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->phpcentClient;
    }

    /**
     * client setter
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->phpcentClient = $client;
        return $this;
    }

    /**
     * Publish data to channel
     * @param ChannelInterface $channel
     * @param Message $message
     * @return bool
     */
    public function publish(ChannelInterface $channel, MessageInterface $message): bool
    {
        try {
            LogHelper::info('Send data to channel', self::LOG_CATEGORY, [
                'channel' => $channel->getName(),
                'message' => $message->getBody(),
            ]);
            Yii::info('Sending data to channel ' . $channel->getName(), self::LOG_CATEGORY);
            $this->client->publish($channel->getName(), $message->getBody());
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), self::LOG_CATEGORY);
            return false;
        }
    }
}
