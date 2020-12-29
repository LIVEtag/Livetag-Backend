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
    const TABLE_NAME = '{{%user}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'role' => $this->enum(['admin', 'seller'])->notNull(),
                'email' => $this->string()->notNull()->unique(),
                'authKey' => $this->string(32)->notNull(),
                'passwordHash' => $this->string()->notNull(),
                'passwordResetToken' => $this->string()->unique(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'createdAt' => $this->unixTimestamp(),
                'updatedAt' => $this->unixTimestamp(),
            ]
        );
    }

    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
