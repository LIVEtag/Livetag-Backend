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
        'sessionId' => Yii::$app->vonage->createSession(), //use one real session to test start
        'createdAt' => $this->generator->incrementalTime - 60,
        'announcedAt' => $this->generator->incrementalTime,
        'duration' => StreamSession::DEFAULT_DURATION,
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
    [
        'id' => StreamSessionFixture::STREAM_SESSION_5_SHOP_2_EXPIRED_UNPUBLISHED,
        'shopId' => ShopFixture::SHOP_2,
        'isPublished' => false,
    ],
    [
        'id' => StreamSessionFixture::STREAM_SESSION_6_SHOP_2_NEW_UNPUBLISHED,
        'shopId' => ShopFixture::SHOP_2,
        'status' => StreamSession::STATUS_NEW,
        'createdAt' => $this->generator->incrementalTime - 60,
        'announcedAt' => $this->generator->incrementalTime,
        'duration' => StreamSession::DEFAULT_DURATION,
        'startedAt' => null,
        'stoppedAt' => null,
        'isPublished' => false,
    ],
    [
        'id' => StreamSessionFixture::STREAM_SESSION_7_SHOP_2_ARCHIVED,
        'shopId' => ShopFixture::SHOP_2,
        'status' => StreamSession::STATUS_ARCHIVED,
        'isPublished' => true,
    ],
];
