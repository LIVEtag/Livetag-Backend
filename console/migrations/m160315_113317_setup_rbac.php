<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\modules\rbac\components\DbManager;
use common\components\db\Migration;
use yii\base\InvalidConfigException;

/**
 * Class m160315_113317_setup_rbac
 */
class m160315_113317_setup_rbac extends Migration
{
    /**
     * @return DbManager
     * @throws InvalidConfigException
     */
    private function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException(
                'You should configure "authManager" component to use database before executing this migration.'
            );
        }

        return $authManager;
    }

    public function up()
    {
        $authManager = $this->getAuthManager();

        $this->createTable(
            $authManager->ruleTable,
            [
                'name' => $this->string(64)->notNull(),
                'data' => $this->text(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY (name)',
            ],
            self::TABLE_OPTIONS
        );

        $this->createTable(
            $authManager->itemTable,
            [
                'name' => $this->string(64)->notNull(),
                'type' => $this->integer()->notNull(),
                'description' => $this->text(),
                'rule_name' => $this->string(64),
                'data' => $this->text(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY (name)',
            ],
            self::TABLE_OPTIONS
        );
        $this->createIndex('index_auth_item_type', $authManager->itemTable, 'type');
        $this->addForeignKey(
            'fk_auth_item_rule_name_to_name',
            $authManager->itemTable,
            'rule_name',
            $authManager->ruleTable,
            'name',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable(
            $authManager->itemChildTable,
            [
                'parent' => $this->string(64)->notNull(),
                'child' => $this->string(64)->notNull(),
                'PRIMARY KEY (parent, child)',
                'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name)'
                . ' ON DELETE CASCADE ON UPDATE CASCADE',
            ],
            self::TABLE_OPTIONS
        );

        $this->addForeignKey(
            'fk_auth_item_child_child_to_name',
            $authManager->itemChildTable,
            'child',
            $authManager->itemTable,
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable(
            $authManager->assignmentTable,
            [
                'item_name' => $this->string(64)->notNull(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'created_at' => $this->integer()->notNull(),
                'PRIMARY KEY (item_name, user_id)',
            ],
            self::TABLE_OPTIONS
        );

        $this->addForeignKey(
            'fk_auth_assignment_item_name_to_name',
            $authManager->assignmentTable,
            'item_name',
            $authManager->itemTable,
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable(
            '{{%rbac_menu}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'name' => $this->string(128)->notNull(),
                'parent' => $this->integer()->unsigned(),
                'route' => $this->string(256)->notNull(),
                'order' => $this->integer()->notNull(),
                'data' => $this->text(),
            ],
            self::TABLE_OPTIONS
        );

        $this->addForeignKey(
            'fk_auth_assignment_user_id_to_user',
            $authManager->assignmentTable,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_rbac_menu_parent_to_rbac_menu_id',
            '{{%rbac_menu}}',
            'parent',
            '{{%rbac_menu}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%rbac_menu}}');
    }
}
