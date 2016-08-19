<?php

use common\components\db\Migration;

/**
 * Handles the creation for table `user_social_profile`.
 */
class m160819_134134_create_user_social_profile_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            '{{%user_social_profile}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'type' => $this->smallInteger()->unsigned()->notNull(),
                'social_id' => $this->string()->notNull(),
                'email' => $this->string()->notNull(),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'expired_at' => $this->integer()->unsigned()->notNull(),
            ],
            self::TABLE_OPTIONS
        );

        $this->createIndex('idx_user_social_profile_user_id', '{{%user_social_profile}}', 'user_id');

        $this->addForeignKey(
            'fk_user_social_profile_to_user',
            '{{%user_social_profile}}',
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
        $this->dropForeignKey('fk_user_social_profile_to_user', '{{%user_social_profile}}');
        $this->dropIndex('idx_user_social_profile_user_id', '{{%user_social_profile}}');
        $this->dropTable('{{%user_social_profile}}');
    }
}
