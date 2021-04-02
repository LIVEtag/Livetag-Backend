<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Stream\StreamSession;

/**
 * Class StreamSessionFixture
 */
class StreamSessionFixture extends ActiveFixture
{
    const STREAM_SESSION_1_SHOP_1_EXPIRED = 1;
    const STREAM_SESSION_2_SHOP_2_EXPIRED = 2;
    const STREAM_SESSION_3_SHOP_1_NEW = 3;
    const STREAM_SESSION_4_SHOP_2_ACTIVE = 4;

    public $modelClass = StreamSession::class;

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        $expiredTime = $this->generator->incrementalTime - 21 * 60 * 60;
        return [
            'sessionId' => '1_MX4' . $this->generator->password(64, 64) . '-QX4',
            'status' => StreamSession::STATUS_STOPPED,
            'createdAt' => $expiredTime - 4 * 60 * 60,
            'announcedAt' => $expiredTime - 3 * 60 * 60,
            'duration' => StreamSession::DEFAULT_DURATION,
            'startedAt' => $expiredTime - 3 * 60 * 60,
            'stoppedAt' => $expiredTime,
        ];
    }
}
