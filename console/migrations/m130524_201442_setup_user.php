<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\db\Migration;
use common\models\User;

/**
 * Class m130524_201442_setup_user
 */
class m130524_201442_setup_user extends Migration
{
    public function up()
    {
        $this->createTable(
            User::tableName(),
            [
                'id' => $this->primaryKey(),
                'username' => $this->string()->notNull()->unique(),
                'auth_key' => $this->string(32)->notNull(),
                'password_hash' => $this->string()->notNull(),
                'password_reset_token' => $this->string()->unique(),
                'email' => $this->string()->notNull()->unique(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
            ],
            self::TABLE_OPTIONS
        );
    }

    public function down()
    {
        $this->dropTable(User::tableName());
    }
}
