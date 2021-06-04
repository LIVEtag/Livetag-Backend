<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Stream\StreamSessionLike;

class StreamSessionLikeFixture extends ActiveFixture
{
    const LIKE_1_SESSION_8 = 1;
    const LIKE_2_SESSION_8 = 2;
    const LIKE_3_SESSION_8 = 3;
    const LIKE_4_SESSION_8 = 4;
    const LIKE_5_SESSION_8 = 5;

    public $modelClass = StreamSessionLike::class;

    public $depends = [
        StreamSessionFixture::class,
        UserFixture::class,
    ];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'createdAt' => $this->generator->incrementalTime,
        ];
    }
}
