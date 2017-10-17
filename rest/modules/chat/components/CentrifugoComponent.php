<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\chat\components;

use Yii;
use yii\base\Model;
use yii\base\InvalidConfigException;
use phpcent\Client;
use rest\modules\chat\models\User;

/**
 * extend from model to use errors
 */
class CentrifugoComponent extends Model
{

    public $secret = 'someSuperSecret';
    public $host = 'http://localhost:8000';
    public $ws = 'ws://localhost:8000/connection/websocket';
    protected $phpcentClient;
    protected $internalUser;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->client = new Client($this->host);
        $this->client->setSecret($this->secret);
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
     */
    public function setClient(Client $client)
    {
        $this->phpcentClient = $client;
    }

    /**
     * internal user getter
     * @return type
     */
    public function getUser(): ?User
    {
        return $this->internalUser;
    }

    /**
     * internal user setter
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->internalUser = $user;
        return $this;
    }

    /**
     * Format of user that we use in centrifugo
     *
     * @return string
     */
    public function getUserInfo()
    {
        if (!$this->user) {
            self::formatUser(new User());
        }
        return self::formatUser($this->user);
    }

    /**
     * Use this format in chat messages (defined by fields() of User model)
     *
     * @param User $user
     * @return array
     */
    public static function formatUser(User $user): array
    {
        return $user->toArray();
    }

    /**
     * get json encoded user info
     * @return string
     */
    public function getJsonEncodedUserInfo(): string
    {
        return json_encode($this->getUserInfo());
    }

    /**
     * generate succes auth response for private channel
     *
     * @param string $channel
     * @param string $client
     * @return array
     */
    public function generateChannelSignResponce($channel, $client): array
    {
        $info = $this->getJsonEncodedUserInfo();
        return [
            'sign' => $this->client->generateChannelSign($client, $channel, $info),
            'info' => $info,
        ];
    }

    /**
     * generate token for current(set) user
     *
     * @return array
     */
    public function generateUserToken(): array
    {
        if (!$this->user) {
            throw new InvalidConfigException(Yii::t('app', 'Please, set user for centrifugo'));
        }
        $timestamp = (string) time();
        $info = $this->getJsonEncodedUserInfo();
        $token = $this->client->generateClientToken($this->user->id, $timestamp, $info);
        return [
            'url' => $this->ws,
            'user' => (string) $this->user->id,
            'timestamp' => $timestamp,
            'info' => $info,
            'token' => $token,
            //private chat access check endpoint
            'authEndpoint' => Yii::$app->urlManager->createAbsoluteUrl('v1/channel/auth'),
            //refresh token (if connection_lifetime in centrifugo config greater than zero)
            'refreshEndpoint' => Yii::$app->urlManager->createAbsoluteUrl('v1/channel/sign'),
        ];
    }

    /**
     * Publish message to channel
     * Epic fail in the implementation:
     * there is no way to publish a message from a specific user.
     * so...put user info in data block
     *
     * @param string $channel
     * @param string $message
     * @return bool
     */
    public function publishMessage(string $channel, string $message): bool
    {
        try {
            Yii::info('Sending message `' . $message . '` to channel `' . $channel . '`', 'centrifugo');
            $this->client->publish($channel, [
                'message' => $message,
                'user' => $this->getUserInfo(),
            ]);
            return true;
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), 'centrifugo');
            return false;
        }
    }
}
