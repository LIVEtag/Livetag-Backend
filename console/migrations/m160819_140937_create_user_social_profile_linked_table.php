<?php

use common\components\db\Migration;

/**
 * Handles the creation for table `user_social_profile_linked`.
 */
class m160819_140937_create_user_social_profile_linked_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            '{{%user_social_profile_linked}}',
            [
                'user_id' => $this->integer()->unsigned()->notNull(),
                'user_social_profile_id' => $this->integer()->unsigned()->notNull()
            ],
            self::TABLE_OPTIONS
        );

        $this->addPrimaryKey(
            'pk_user_social_profile_linked',
            '{{%user_social_profile_linked}}',
            ['user_id', 'user_social_profile_id']
        );

        $this->addForeignKey(
            'fk_user_social_profile_linked_to_user',
            '{{%user_social_profile_linked}}',
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
        //$this->dropForeignKey('fk_user_social_profile_linked_to_user', '{{%user_social_profile_linked}}');
        $this->dropTable('{{%user_social_profile_linked}}');
    }
}
