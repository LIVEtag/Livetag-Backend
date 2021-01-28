<?php

use common\fixtures\ShopFixture;
use common\fixtures\StreamSessionFixture;
use common\models\Stream\StreamSession;

return [
    [
        'id' => StreamSessionFixture::STREAM_EXPIRED_1,
        'shopId' => ShopFixture::STORE_1
    ],
    [
        'id' => StreamSessionFixture::STREAM_EXPIRED_2,
        'shopId' => ShopFixture::STORE_2
    ],
    [
        'id' => StreamSessionFixture::STREAM_NEW,
        'shopId' => ShopFixture::STORE_1,
        'status' => StreamSession::STATUS_NEW,
        'sessionId' => Yii::$app->vonage->createSession(),//use one real session to test start
        'createdAt' => $this->generator->incrementalTime - 60,
        'startedAt' => null,
        'stoppedAt' => null,
    ],
    [
        'id' => StreamSessionFixture::STREAM_ACTIVE,
        'shopId' => ShopFixture::STORE_2,
        'status' => StreamSession::STATUS_ACTIVE,
        'createdAt' => $this->generator->incrementalTime - 60,
        'startedAt' => $this->generator->incrementalTime,
        'stoppedAt' => null,
    ],
];
