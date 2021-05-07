<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210413131818AddRotateToStreamSession
 */
class M210413131818AddRotateToStreamSession extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(StreamSession::TABLE_NAME, 'rotate', $this->enum(['0', '90', '180', '270'])->notNull()->defaultValue('0'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(StreamSession::TABLE_NAME, 'rotate');
    }
}
