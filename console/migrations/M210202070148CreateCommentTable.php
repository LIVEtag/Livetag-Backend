<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M130524201442SetupUser as User;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210202070148CreateCommentTable
 */
class M210202070148CreateCommentTable extends Migration
{
    const TABLE_NAME = '{{%comment}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'userId' => $this->integer()->unsigned()->notNull(),
                'streamSessionId' => $this->integer()->unsigned()->notNull(),
                'message' => $this->text()->notNull(),
                'createdAt' => $this->unixTimestamp(),
                'updatedAt' => $this->unixTimestamp(),
            ]
        );
        $this->addFK(self::TABLE_NAME, 'userId', User::TABLE_NAME, 'id', 'CASCADE');
        $this->addFK(self::TABLE_NAME, 'streamSessionId', StreamSession::TABLE_NAME, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, StreamSession::TABLE_NAME);
        $this->dropFK(self::TABLE_NAME, User::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
