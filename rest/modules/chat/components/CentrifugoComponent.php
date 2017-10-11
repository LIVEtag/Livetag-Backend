<?php
namespace common\components\chat;

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
}
