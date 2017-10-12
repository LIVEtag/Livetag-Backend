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
        return json_encode([
            'id' => $this->user->id,
            'name' => $this->user->username
        ]);
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
        $info = $this->getUserInfo();
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
        $timestamp = (string) time();
        $info = $this->getUserInfo();
        $token = $this->client->generateClientToken($this->user->id, $timestamp, $info);
        return [
            'token' => $token
        ];
    }
}
