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
                'email' => $this->string()->notNull()->unique(),
                'authKey' => $this->string(32)->notNull(),
                'passwordHash' => $this->string()->notNull(),
                'passwordResetToken' => $this->string()->unique(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'createdAt' => $this->integer()->unsigned()->notNull(),
                'updatedAt' => $this->integer()->unsigned()->notNull(),
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
