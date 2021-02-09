<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210105142621CreateStreamSessionTokenTable
 */
class M210105142621CreateStreamSessionTokenTable extends Migration
{
    const TABLE_NAME = '{{%stream_session_token}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'streamSessionId' => $this->integer()->unsigned()->notNull(),
                'token' => $this->string(512)->notNull(),
                'createdAt' => $this->unixTimestamp(),
                'expiredAt' => $this->unixTimestamp()->comment('Token expiration time'),
            ]
        );

        $this->addFK(self::TABLE_NAME, 'streamSessionId', StreamSession::TABLE_NAME, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, StreamSession::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
