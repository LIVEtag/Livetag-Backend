<?php
use common\fixtures\ShopFixture;
use common\models\Stream\StreamSession;

return [
    [
        'shopId' => ShopFixture::STORE_1,
        'sessionId' => '2_MX40NzA2Nzg5NH5-session1-fg',
        'publisherToken' => 'publisherToken1',
    ],
    [
        'shopId' => ShopFixture::STORE_2,
        'sessionId' => '2_MX40NzA2Nzg5NH5-session2-fg',
        'publisherToken' => 'publisherToken2',
    ],
    [
        'shopId' => ShopFixture::STORE_2,
        'sessionId' => '2_MX40NzA2Nzg5NH5-session3-fg',
        'publisherToken' => 'publisherToken3',
        'status' => StreamSession::STATUS_ACTIVE,
        'createdAt' => $this->generator->incrementalTime,
        'updatedAt' => $this->generator->incrementalTime,
        'expiredAt' => $this->generator->incrementalTime + 3 * 60 * 60,
    ],
];
