<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210427101237EmptySession
 */
class M210427101237EmptySession extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(StreamSession::TABLE_NAME, 'sessionId', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update(StreamSession::TABLE_NAME, ['sessionId' => ''], ['sessionId' => null]);
        $this->alterColumn(StreamSession::TABLE_NAME, 'sessionId', $this->string(255)->notNull());
    }
}
