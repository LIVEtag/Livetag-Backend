<?php

use common\fixtures\StreamSessionArchiveFixture;
use common\fixtures\StreamSessionFixture;
use common\models\Stream\StreamSessionArchive;

/** @var StreamSessionArchiveFixture $this */

return [
    [
        // Manually uploaded
        'id' => StreamSessionArchiveFixture::ARCHIVE_1_SESSION_7_NEW,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_7_SHOP_2_ARCHIVED,
        'externalId' => null,
        'path' => $this->generateVideo('video'),
        'originName' => $this->generator->word() . '.mp4',
        'size' => 158143,
        'status' => StreamSessionArchive::STATUS_NEW,
    ],
];