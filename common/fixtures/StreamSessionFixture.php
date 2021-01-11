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
    const STREAM_EXPIRED_1 = 1;
    const STREAM_EXPIRED_2 = 2;

    public $modelClass = StreamSession::class;

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        $expiredTime = $this->generator->incrementalTime - 21 * 60 * 60;
        return [
            'status' => StreamSession::STATUS_STOPPED,
            'createdAt' => $expiredTime - 3 * 60 * 60,
            'updatedAt' => $expiredTime,
            'expiredAt' => $expiredTime,
        ];
    }
}
