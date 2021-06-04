<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Analytics;

use common\models\Analytics\StreamSessionEvent;
use common\models\Stream\StreamSession;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class EventForm extends Model
{
    /** @var string */
    public $type;

    /** @var array */
    public $payload;

    /** @var StreamSession */
    private $streamSession;

    /** @var User */
    private $user;

    /**
     * @param StreamSession $streamSession
     * @param User $user
     */
    public function __construct(StreamSession $streamSession, User $user)
    {
        parent::__construct([]);
        $this->streamSession = $streamSession;
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['type', 'string'],
            ['payload', 'validateOption'],
        ];
    }

    /**
     * Validate option belongs to product!
     * @param string $attribute
     */
    public function validateOption($attribute)
    {
        if (!ArrayHelper::isIn($this->$attribute, $this->product->options)) {
            $this->addError($attribute, Yii::t('app', 'The product does not contain such an option.'));
        }
    }

    /**
     * @return StreamSessionEvent|self
     */
    public function create()
    {
        if (!$this->validate()) {
            return $this;
        }

        /** @var StreamSessionEvent $event */
        $event = new StreamSessionEvent();
        $event->userId = $this->user->getId();
        $event->streamSessionId = $this->streamSession->getId();
        $event->type = $this->type;
        $event->payload = $this->payload;
        $event->save();
        return $event; //return model or errors
    }
}
