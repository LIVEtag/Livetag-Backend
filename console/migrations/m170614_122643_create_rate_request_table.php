<?php

use common\components\db\Migration;

/**
 * Handles the creation of table `rate_request`.
 */
class m170614_122643_create_rate_request_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            '{{%rate_request}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'action_id' => $this->string()->notNull(),
                'ip' => $this->string()->notNull(),
                'user_agent' => $this->string()->notNull(),
                'count' => $this->smallInteger()->unsigned()->notNull(),
                'created_at' => $this->integer()->unsigned(),
                'last_request' => $this->integer()->unsigned()->notNull(),
                'access' => $this->boolean()->defaultValue(true),
            ],
            self::TABLE_OPTIONS
        );

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%rate_request}}');
    }
}
