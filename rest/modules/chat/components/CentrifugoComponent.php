<?php
namespace rest\modules\chat\components;

use Yii;
use yii\base\Model;
use yii\base\InvalidConfigException;
use phpcent\Client;

/**
 * extend from model to use errors
 */
class CentrifugoComponent extends Model
{

    public $secret = 'someSecret';
    public $host = 'http://localhost:8000';
    public $ws;
    protected $phpcentClient;
    protected $operationUser;

    public function init()
    {
        $this->client = new Client($this->host);
        $this->client->setSecret($this->secret);
    }

    public function getClient()
    {
        return $this->phpcentClient;
    }

    public function setClient($client)
    {
        $this->phpcentClient = $client;
    }

    public function setUser($user)
    {
        $this->operationUser = $user;
        return $this; //test it
    }

    public function getUser()
    {
        return $this->operationUser;
    }

    /**
     * Format of user that we use in centrifugo
     *
     * @return string
     */
    public function getUserInfo()
    {
        if (!$this->user) {
            return json_encode([
                'id' => '',
                'name' => 'Unknown'
            ]);
        }
        return self::formatUser($this->user);
    }

    /**
     * Use this format in chat messages
     * @param type $user
     * @return type
     */
    public static function formatUser($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->username
        ];
    }

    public function getJsonEncodedUserInfo()
    {
        return json_encode($this->getUserInfo());
    }

    /**
     * generate succes auth response for private channel
     *
     * @param string $channel
     * @param string $client
     * @return type
     */
    public function generateChannelSignResponce($channel, $client)
    {
        $info = $this->getJsonEncodedUserInfo();
        return [
            'sign' => $this->client->generateChannelSign($client, $channel, $info),
            'info' => $info,
        ];
    }

    /**
     * generate token for current(set) user
     * @return type
     */
    public function generateUserToken()
    {
        if (!$this->user) {
            throw new InvalidConfigException(Yii::t('app', 'Please, set user for centrifugo'));
        }
        $timestamp = (string) time();
        $info = $this->getJsonEncodedUserInfo();
        $token = $this->client->generateClientToken($this->user->id, $timestamp, $info);

        return [
            'url' => 'ws://centrifugo.local:8000/connection/websocket',
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
     * Epic feil in the implementation:
     * there is no way to publish a message from a specific user.
     * so...put user info in data block
     *
     * @param string $channel
     * @param string $message
     * @return boolean
     */
    public function publishMessage(string $channel, string $message)
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
