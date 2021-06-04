<?php
use common\fixtures\CommentFixture;
use common\fixtures\StreamSessionFixture;
use common\fixtures\UserFixture;

return [
    CommentFixture::COMMENT_1_SESSION_1_BUYER_1 => [
        'id' => CommentFixture::COMMENT_1_SESSION_1_BUYER_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'userId' => UserFixture::BUYER_1,
    ],
    CommentFixture::COMMENT_2_SESSION_1_BUYER_2 => [
        'id' => CommentFixture::COMMENT_2_SESSION_1_BUYER_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'userId' => UserFixture::BUYER_2,
    ],
    CommentFixture::COMMENT_3_SESSION_1_SELLER_10 => [
        'id' => CommentFixture::COMMENT_3_SESSION_1_SELLER_10,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'userId' => UserFixture::SELLER_1,
    ],
    CommentFixture::COMMENT_4_SESSION_2_BUYER_1 => [
        'id' => CommentFixture::COMMENT_4_SESSION_2_BUYER_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'userId' => UserFixture::BUYER_1,
    ],
    CommentFixture::COMMENT_5_SESSION_2_BUYER_2 => [
        'id' => CommentFixture::COMMENT_5_SESSION_2_BUYER_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_2_SHOP_2_EXPIRED,
        'userId' => UserFixture::BUYER_2,
    ],
    CommentFixture::COMMENT_6_SESSION_4_BUYER_1 => [
        'id' => CommentFixture::COMMENT_6_SESSION_4_BUYER_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'userId' => UserFixture::BUYER_1,
    ],
    CommentFixture::COMMENT_7_SESSION_4_BUYER_2 => [
        'id' => CommentFixture::COMMENT_7_SESSION_4_BUYER_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'userId' => UserFixture::BUYER_2,
    ],
    CommentFixture::COMMENT_8_SESSION_4_BUYER_1 => [
        'id' => CommentFixture::COMMENT_8_SESSION_4_BUYER_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'userId' => UserFixture::BUYER_1,
    ],
    CommentFixture::COMMENT_9_SESSION_4_SELLER_2 => [
        'id' => CommentFixture::COMMENT_9_SESSION_4_SELLER_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'userId' => UserFixture::SELLER_2,
    ],
    CommentFixture::COMMENT_10_SESSION_4_BUYER_2 => [
        'id' => CommentFixture::COMMENT_10_SESSION_4_BUYER_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'userId' => UserFixture::BUYER_2,
    ],
    CommentFixture::COMMENT_11_SESSION_1_SELLER_1 => [
        'id' => CommentFixture::COMMENT_11_SESSION_1_SELLER_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'userId' => UserFixture::SELLER_1,
        'parentCommentId' => CommentFixture::COMMENT_1_SESSION_1_BUYER_1,
    ],
    CommentFixture::COMMENT_12_SESSION_1_SELLER_1 => [
        'id' => CommentFixture::COMMENT_12_SESSION_1_SELLER_1,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_1_SHOP_1_EXPIRED,
        'userId' => UserFixture::SELLER_1,
        'parentCommentId' => CommentFixture::COMMENT_2_SESSION_1_BUYER_2,
    ],
    CommentFixture::COMMENT_13_SESSION_4_SELLER_2 => [
        'id' => CommentFixture::COMMENT_13_SESSION_4_SELLER_2,
        'streamSessionId' => StreamSessionFixture::STREAM_SESSION_4_SHOP_2_ACTIVE,
        'userId' => UserFixture::SELLER_2,
        'parentCommentId' => CommentFixture::COMMENT_10_SESSION_4_BUYER_2,
    ],
];
