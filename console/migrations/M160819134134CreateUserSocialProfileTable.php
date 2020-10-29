<?php
namespace console\migrations;

use common\components\db\Migration;

/**
 * Handles the creation for table `user_social_profile`.
 */
class M160819134134CreateUserSocialProfileTable extends Migration
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
                'userId' => $this->integer()->unsigned()->notNull(),
                'type' => $this->smallInteger()->unsigned()->notNull(),
                'socialId' => $this->string()->notNull(),
                'email' => $this->string()->notNull(),
                'createdAt' => $this->integer()->unsigned()->notNull(),
                'updatedAt' => $this->integer()->unsigned()->notNull(),
            ]
        );

        $this->createIndex('idx_user_social_profile_userId', '{{%user_social_profile}}', 'userId');

        $this->addForeignKey(
            'fk_user_social_profile_to_user',
            '{{%user_social_profile}}',
            'userId',
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
        $this->dropIndex('idx_user_social_profile_userId', '{{%user_social_profile}}');
        $this->dropTable('{{%user_social_profile}}');
    }
}
