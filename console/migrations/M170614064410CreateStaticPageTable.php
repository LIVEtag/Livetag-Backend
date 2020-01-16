<?php
namespace console\migrations;

use common\components\db\Migration;

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
            'content' => $this->longText()->notNull(),
            'metaTitle' => $this->string()->notNull(),
            'metaDescription' => $this->text()->notNull(),
            'slug' => $this->string()->notNull()->unique(),
            'sortOrder' => $this->integer()->unsigned()->notNull(),
            'createdAt' => $this->integer()->unsigned()->notNull(),
            'updatedAt' => $this->integer()->unsigned()->notNull(),
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
