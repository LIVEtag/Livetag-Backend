<?php
namespace rest\modules\chat\migrations;

class M171011114726ChatTables extends Migration
{

    public function up()
    {
        $this->createTable(
            '{{%channel}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'url'=>$this->string(255)->notNull(),
                'name'=>$this->string(255)->notNull(),
                'description'=>$this->string(255)->notNull(),
                'type'=>$this->boolean()->notNull()->comment('1-public; 2-private')->defaultValue(1),
                'created_by' => $this->integer()->unsigned()->notNull(),
                'updated_by' => $this->integer()->unsigned()->notNull(),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'updated_at' => $this->integer()->unsigned()->notNull(),
            ],
            self::TABLE_OPTIONS
        );
        $this->createIndex('idx_channel_url', '{{%channel}}', 'url');

        $this->createTable(
            '{{%channel_access}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'channel_id' => $this->integer()->unsigned()->notNull(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'role' => $this->boolean()->notNull()->comment('1-user; 2-admin')->defaultValue(1),
            ],
            self::TABLE_OPTIONS
        );
        $this->addForeignKey(
            'fk_channel_access_to_user',
            '{{%channel_access}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_channel_access_to_channel',
            '{{%channel_access}}',
            'channel_id',
            '{{%channel}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable(
            '{{%message}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'channel_id' => $this->integer()->unsigned()->notNull(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'updated_at' => $this->integer()->unsigned()->notNull(),
            ],
            self::TABLE_OPTIONS
        );
        $this->addForeignKey(
            'fk_message_to_user',
            '{{%message}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_message_to_channel',
            '{{%message}}',
            'channel_id',
            '{{%channel}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_message_to_channel', '{{%message}}');
        $this->dropForeignKey('fk_message_to_user', '{{%message}}');
        $this->dropForeignKey('fk_channel_access_to_channel', '{{%channel_access}}');
        $this->dropForeignKey('fk_channel_access_to_user', '{{%channel_access}}');

        $this->dropTable('{{%channel}}');
        $this->dropTable('{{%message}}');
        $this->dropTable('{{%channel_access}}');
    }
}
