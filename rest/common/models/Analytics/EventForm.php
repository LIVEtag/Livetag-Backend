<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Analytics;

use common\models\Analytics\StreamSessionProductEvent;
use common\models\Comment\Comment;
use common\models\Product\Product;
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

    /** @var Product */
    private $product;

    /** @var User */
    private $user;

    /**
     * @param StreamSession $streamSession
     * @param User $user
     */
    public function __construct(StreamSession $streamSession, Product $product, User $user)
    {
        parent::__construct([]);
        $this->streamSession = $streamSession;
        $this->product = $product;
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
     * @return Comment|self
     */
    public function create()
    {
        if (!$this->validate()) {
            return $this;
        }

        /** @var StreamSessionProductEvent $event */
        $event = new StreamSessionProductEvent();
        $event->scenario = StreamSessionProductEvent::SCENARIO_USER;
        $event->userId = $this->user->getId();
        $event->streamSessionId = $this->streamSession->getId();
        $event->productId = $this->product->getId();
        $event->type = $this->type;
        $event->payload =  $this->payload;

        $event->save();
        return $event; //return model or errors
    }
}
