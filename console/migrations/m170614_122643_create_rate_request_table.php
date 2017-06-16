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
                'action_type' => $this->string()->notNull(),
                'user_id' => $this->integer()->unsigned(),
                'ip' => $this->string()->notNull(),
                'user_agent' => $this->string()->notNull(),
                'count' => $this->smallInteger()->unsigned()->notNull(),
                'created_at' => $this->integer()->unsigned(),
                'last_request' => $this->integer()->unsigned()->notNull(),
                'access' => $this->boolean()->defaultValue(true),
            ],
            self::TABLE_OPTIONS
        );

        $this->createIndex('idx_rate_request_user_id', '{{%rate_request}}', 'user_id');

        $this->addForeignKey(
            'fk_rate_request_to_user',
            '{{%rate_request}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_rate_request_to_user', '{{%rate_request}}');
        $this->dropIndex('idx_rate_request_user_id', '{{%rate_request}}');
        $this->dropTable('{{%rate_request}}');
    }
}
