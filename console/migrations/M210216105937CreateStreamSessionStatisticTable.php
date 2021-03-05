<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210216105937CreateStreamSessionStatisticTable
 */
class M210216105937CreateStreamSessionStatisticTable extends Migration
{
    const TABLE_NAME = '{{%stream_session_statistic}}';

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
                'addToCartCount' => $this->integer()->unsigned()->defaultValue(0),
                'viewsCount' => $this->integer()->unsigned()->defaultValue(0),
            ]
        );
        $this->addFK(self::TABLE_NAME, 'streamSessionId', StreamSession::TABLE_NAME, 'id', 'CASCADE');
        $this->createIndex('idx_addToCartCount', self::TABLE_NAME, 'addToCartCount');
        $this->createIndex('idx_viewsCount', self::TABLE_NAME, 'viewsCount');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, StreamSession::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
