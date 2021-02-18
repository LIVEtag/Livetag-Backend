<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M130524201442SetupUser as User;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210218075921CreateStreamSessionEventTable
 */
class M210218075921CreateStreamSessionEventTable extends Migration
{
    const TABLE_NAME = '{{%stream_session_event}}';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'streamSessionId' => $this->integer()->unsigned()->notNull(),
                'userId' => $this->integer()->unsigned()->notNull(),
                'type' => $this->enum(['view'])->notNull(),
                'payload' => $this->json()->null(),
                'createdAt' => $this->unixTimestamp(),
            ]
        );
        $this->addFK(self::TABLE_NAME, 'streamSessionId', StreamSession::TABLE_NAME, 'id', 'CASCADE');
        $this->addFK(self::TABLE_NAME, 'userId', User::TABLE_NAME, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, User::TABLE_NAME);
        $this->dropFK(self::TABLE_NAME, StreamSession::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
