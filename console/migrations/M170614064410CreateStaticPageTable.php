<?php
namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `static_page`.
 */
class M170614064410CreateStaticPageTable extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%static_page}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'content' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->notNull(),
            'meta_title' => $this->string()->notNull(),
            'meta_description' => $this->text()->notNull(),
            'slug' => $this->string()->notNull()->unique(),
            'sort_order' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%static_page}}');
    }
}
