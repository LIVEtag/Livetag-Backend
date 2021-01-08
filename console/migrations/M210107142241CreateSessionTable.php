<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M130524201442SetupUser as User;

/**
 * Class M210107142241CreateSessionTable
 */
class M210107142241CreateSessionTable extends Migration
{
    const TABLE_NAME = '{{%session}}';
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->char(40)->notNull(),
                'expire' => $this->unixTimestamp(),
                'data' => $this->binary(),
                'userId' => $this->integer()->unsigned(),
                'agent' => $this->string(255),
                'ip' => $this->string(255),
            ]
        );
    
        $this->addPrimaryKey('session_pk', 'session', 'id');
        $this->addFK(self::TABLE_NAME, 'userId', User::TABLE_NAME, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, User::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
