<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Stream\StreamSessionToken;

/**
 * Class StreamSessionTokenFixture
 */
class StreamSessionTokenFixture extends ActiveFixture
{
    const TOKEN_ACTIVE = 1;

    public $modelClass = StreamSessionToken::class;
    public $depends = [StreamSessionFixture::class];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'token' => 'T1==' . $this->generator->password(356, 356),
            'createdAt' => $this->generator->incrementalTime,
            'expiredAt' => $this->generator->incrementalTime + 3 * 60 * 60,
        ];
    }
}
