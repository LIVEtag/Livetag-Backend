<?php

use common\fixtures\ShopFixture;
use common\fixtures\StreamSessionFixture;
use common\models\Stream\StreamSession;

return [
    [
        'id' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'shopId' => ShopFixture::SHOP_1
    ],
    [
        'id' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'shopId' => ShopFixture::SHOP_2
    ],
    [
        'id' => StreamSessionFixture::STREAM_SESSION_3_SHOP_1_NEW,
        'shopId' => ShopFixture::SHOP_1,
        'status' => StreamSession::STATUS_NEW,
        'sessionId' => Yii::$app->vonage->createSession(),//use one real session to test start
        'createdAt' => $this->generator->incrementalTime - 60,
        'startedAt' => null,
        'stoppedAt' => null,
    ],
    [
        'id' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'shopId' => ShopFixture::SHOP_2,
        'status' => StreamSession::STATUS_ACTIVE,
        'createdAt' => $this->generator->incrementalTime - 60,
        'startedAt' => $this->generator->incrementalTime,
        'stoppedAt' => null,
    ],
];
