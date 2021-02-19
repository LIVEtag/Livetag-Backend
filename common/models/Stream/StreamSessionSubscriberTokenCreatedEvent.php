<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
declare(strict_types=1);

namespace common\models\Stream;

use yii\base\Event;
use yii\web\IdentityInterface;

/**
 * New subscriber token created
 */
class StreamSessionSubscriberTokenCreatedEvent extends Event
{
    /**
     * @var IdentityInterface
     */
    public $user;

    /**
     * @inheritdoc
     */
    public function __construct(IdentityInterface $user, $config = array())
    {
        $this->user = $user;
        parent::__construct($config);
    }
}
