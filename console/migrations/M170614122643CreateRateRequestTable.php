<?php
namespace console\migrations;

use common\components\db\Migration;

/**
 * Handles the creation of table `rate_request`.
 */
class M170614122643CreateRateRequestTable extends Migration
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
                'count' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'last_request' => $this->integer()->unsigned()->notNull(),
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