<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210202070148CreateCommentTable as Comment;

/**
 * Class M210517173707AddParentCommentIdToComment
 */
class M210517173707AddParentCommentIdToComment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            Comment::TABLE_NAME,
            'parentCommentId',
            $this->integer()->unsigned()->after('message'),
        );

        $this->addColumn(
            Comment::TABLE_NAME,
            'status',
            $this->tinyInteger()->notNull()->defaultValue(10)->after('parentCommentId'),
        );

        $this->addFK(Comment::TABLE_NAME, 'parentCommentId', Comment::TABLE_NAME, 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropFK(Comment::TABLE_NAME, Comment::TABLE_NAME);
        $this->dropColumn(Comment::TABLE_NAME, 'parentCommentId');
        $this->dropColumn(Comment::TABLE_NAME, 'status');
    }
}
