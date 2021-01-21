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
                'role' => $this->enum(['admin', 'seller', 'buyer'])->notNull(),
                'email' => $this->string()->null()->unique()->comment('email- unique identifier for seller and admin'),
                'uuid' => $this->string(36)->null()->unique()->comment('uuid - unique identifier for buyer'),
                'name' => $this->string(),
                'authKey' => $this->string(32)->null(),
                'passwordHash' => $this->string()->null(),
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
