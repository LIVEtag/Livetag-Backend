<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M201229133425CreateShopTable as Shop;

/**
 * Class M210105142620CreateStreamSessionTable
 */
class M210105142620CreateStreamSessionTable extends Migration
{
    const TABLE_NAME = '{{%stream_session}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'shopId' => $this->integer()->unsigned()->notNull(),
                'status' => $this->smallInteger()->notNull()->defaultValue(1),
                'sessionId' => $this->string(255)->notNull(), //A session ID string can be up to 255 characters long.
                'createdAt' => $this->unixTimestamp(),
                'startedAt' => $this->integer()->unsigned()->null()->comment('The actual start time (when the seller clicks start and receives a token)'),
                'stoppedAt' => $this->integer()->unsigned()->null()->comment('The actual end time (when the seller stops broadcasting)'),
            ]
        );
        $this->addFK(self::TABLE_NAME, 'shopId', Shop::TABLE_NAME, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, Shop::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
