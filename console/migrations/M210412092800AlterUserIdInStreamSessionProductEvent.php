<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210212071343CreateStreamSessionProductEventTable as StreamSessionProductEvent;

/**
 * Class M210412092800AlterUserIdInStreamSessionProductEvent
 */
class M210412092800AlterUserIdInStreamSessionProductEvent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(StreamSessionProductEvent::TABLE_NAME, 'userId', $this->integer()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(StreamSessionProductEvent::TABLE_NAME, ['userId' => null]);
        $this->alterColumn(StreamSessionProductEvent::TABLE_NAME, 'userId', $this->integer()->unsigned()->notNull());
    }
}
