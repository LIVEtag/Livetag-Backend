<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210414144537CreateStreamSessionArchiveTable
 */
class M210414144537CreateStreamSessionArchiveTable extends Migration
{
    const TABLE_NAME = '{{%stream_session_archive}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'streamSessionId' => $this->integer()->unsigned()->notNull(),
                'externalId' => $this->string(255),
                'path' => $this->string(255)->notNull(),
                'playlist' => $this->string(255),
                'originName' => $this->string(255)->notNull(),
                'size' => $this->integer()->notNull(),
                'type' => $this->enum(['video'])->notNull(),
                'status' => $this->smallInteger()->notNull()->defaultValue(1),
                'createdAt' => $this->unixTimestamp(),
                'updatedAt' => $this->unixTimestamp(),
            ]
        );

        $this->addFK(self::TABLE_NAME, 'streamSessionId', StreamSession::TABLE_NAME, 'id', 'CASCADE');
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
