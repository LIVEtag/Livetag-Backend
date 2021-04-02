<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210329115415CreateStreamSessionCoverTable
 */
class M210329115415CreateStreamSessionCoverTable extends Migration
{
   const TABLE_NAME = '{{%stream_session_cover}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'streamSessionId' => $this->integer()->unsigned()->notNull(),
            'path' => $this->string(255),
            'originName' => $this->string(255),
            'size' => $this->integer()->unsigned(),
            'type' => $this->enum(["image", "video"])->notNull(),
            'createdAt' => $this->unixTimestamp()
            ], self::TABLE_OPTIONS);

        $this->addFK(self::TABLE_NAME, 'streamSessionId', StreamSession::TABLE_NAME, 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropFK(self::TABLE_NAME, StreamSession::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
