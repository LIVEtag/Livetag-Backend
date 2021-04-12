<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210212071343CreateStreamSessionProductEventTable as StreamSessionProductEvent;

/**
 * Class M210411203450UpdateTypeInStreamSessionProductEvent
 */
class M210411203450AlterTypeInStreamSessionProductEvent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(StreamSessionProductEvent::TABLE_NAME, 'type', $this->enum(['addToCart', 'productCreate', 'productUpdate', 'productDelete'])->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn(StreamSessionProductEvent::TABLE_NAME, 'type', $this->enum(['addToCart'])->notNull());
    }
}
