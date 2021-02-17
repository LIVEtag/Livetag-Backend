<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Analytics\StreamSessionProductEvent;

/**
 * Class StreamSessionProductFixture
 */
class StreamSessionProductEventFixture extends ActiveFixture
{
    //Expired Session of Shop1
    const EVENT_1_SESSION_1 = 1;
    const EVENT_2_SESSION_1 = 2;
    const EVENT_3_SESSION_1 = 3;
    const EVENT_4_SESSION_1 = 4;
    const EVENT_5_SESSION_1 = 5;
    //Expired Session of Shop2
    const EVENT_6_SESSION_2 = 6;
    const EVENT_7_SESSION_2 = 7;
    const EVENT_8_SESSION_2 = 8;
    const EVENT_9_SESSION_2 = 9;
    //Active Session of Shop2
    const EVENT_10_SESSION_4 = 10;
    const EVENT_11_SESSION_4 = 11;

    public $modelClass = StreamSessionProductEvent::class;
    public $depends = [
        ProductFixture::class,
        StreamSessionFixture::class,
        UserFixture::class,
    ];

    /** @inheritdoc */
    public $requiredAttributes = ['streamSessionId', 'productId', 'userId'];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'type' => StreamSessionProductEvent::TYPE_ADD_TO_CART, //for now only one event type
            'createdAt' => $this->generator->incrementalTime,
        ];
    }
}
