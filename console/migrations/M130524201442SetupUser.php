<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace console\migrations;

use common\components\db\Migration;

/**
 * Class M130524201442SetupUser
 */
class M130524201442SetupUser extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%user}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'role'=>$this->string()->notNull(),
                'username' => $this->string()->notNull()->unique(),
                'auth_key' => $this->string(32)->notNull(),
                'password_hash' => $this->string()->notNull(),
                'password_reset_token' => $this->string()->unique(),
                'email' => $this->string()->notNull()->unique(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'updated_at' => $this->integer()->unsigned()->notNull(),
            ],
            self::TABLE_OPTIONS
        );
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
