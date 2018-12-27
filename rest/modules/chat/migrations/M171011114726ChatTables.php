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
                'createdBy' => $this->integer()->unsigned()->notNull(),
                'updatedBy' => $this->integer()->unsigned()->notNull(),
                'createdAt' => $this->integer()->unsigned()->notNull(),
                'updatedAt' => $this->integer()->unsigned()->notNull(),
            ],
            self::TABLE_OPTIONS
        );
        $this->createIndex('idx_channel_url', '{{%channel}}', 'url');

        $this->createTable(
            '{{%channel_user}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'channelId' => $this->integer()->unsigned()->notNull(),
                'userId' => $this->integer()->unsigned()->notNull(),
                'role' => $this->boolean()->notNull()->comment('1-user; 2-admin')->defaultValue(1),
            ],
            self::TABLE_OPTIONS
        );
        
        $this->createIndex('idx_channel_user_unique', '{{%channel_user}}', 'channelId,userId,role', true);

        $this->addForeignKey(
            'fk_channel_user_to_user',
            '{{%channel_user}}',
            'userId',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_channel_user_to_channel',
            '{{%channel_user}}',
            'channelId',
            '{{%channel}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable(
            '{{%channel_message}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'channelId' => $this->integer()->unsigned()->notNull(),
                'userId' => $this->integer()->unsigned()->notNull(),
                'message' => $this->string(255)->notNull(),
                'createdAt' => $this->integer()->unsigned()->notNull(),
                'updatedAt' => $this->integer()->unsigned()->notNull(),
            ],
            self::TABLE_OPTIONS
        );
        $this->addForeignKey(
            'fk_channel_message_to_user',
            '{{%channel_message}}',
            'userId',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_channel_message_to_channel',
            '{{%channel_message}}',
            'channelId',
            '{{%channel}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_channel_message_to_channel', '{{%channel_message}}');
        $this->dropForeignKey('fk_channel_message_to_user', '{{%channel_message}}');
        $this->dropForeignKey('fk_channel_user_to_channel', '{{%channel_user}}');
        $this->dropForeignKey('fk_channel_user_to_user', '{{%channel_user}}');

        $this->dropTable('{{%channel}}');
        $this->dropTable('{{%channel_message}}');
        $this->dropTable('{{%channel_user}}');
    }
}
