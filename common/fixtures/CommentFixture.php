<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Comment\Comment;

/**
 * Class CommentFixture
 */
class CommentFixture extends ActiveFixture
{
    //Stopped Session of Shop1
    const COMMENT_1_SESSION_1_BUYER_1 = 1;
    const COMMENT_2_SESSION_1_BUYER_2 = 2;
    const COMMENT_3_SESSION_1_SELLER_10 = 3;
    //Stopped Session of Shop2
    const COMMENT_4_SESSION_2_BUYER_1 = 4;
    const COMMENT_5_SESSION_2_BUYER_2 = 5;
    //Active Session of Shop2
    const COMMENT_6_SESSION_4_BUYER_1 = 6;
    const COMMENT_7_SESSION_4_BUYER_2 = 7;
    const COMMENT_8_SESSION_4_BUYER_1 = 8;
    const COMMENT_9_SESSION_4_SELLER_2 = 9;
    const COMMENT_10_SESSION_4_BUYER_2 = 10;

    public $modelClass = Comment::class;
    public $depends = [
        UserFixture::class,
        StreamSessionFixture::class,
    ];

    /** @inheritdoc */
    public $requiredAttributes = ['userId', 'streamSessionId'];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'message' => $this->generator->text(255),
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }
}
