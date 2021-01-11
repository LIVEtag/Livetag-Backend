<?php

namespace console\migrations;

use common\components\db\Migration;

/**
 * Class M210107142241CreateSessionTable
 */
class M210107142241CreateSessionTable extends Migration
{
    const TABLE_NAME = '{{%session}}';
    const PK_SESSION = 'session_pk';
    
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->string()->notNull(),
                'expire' => $this->unixTimestamp(),
                'data' => $this->binary(),
            ]
        );
        $this->addPrimaryKey(self::PK_SESSION, 'session', 'id');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
