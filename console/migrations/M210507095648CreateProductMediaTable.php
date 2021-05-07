<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210111122050CreateProductTable as Product;

/**
 * Class M210507095648CreateProductMediaTable
 */
class M210507095648CreateProductMediaTable extends Migration
{
    const TABLE_NAME = '{{%product_media}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'productId' => $this->integer()->unsigned()->notNull(),
                'path' => $this->string(255)->notNull(),
                'originName' => $this->string(255)->notNull(),
                'size' => $this->integer()->unsigned()->notNull(),
                'formatted' => $this->json()->notNull(),
                'type' => $this->enum(["image"])->notNull(),
                'createdAt' => $this->unixTimestamp(),
            ]
        );

        $this->addFK(self::TABLE_NAME, 'productId', Product::TABLE_NAME, 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropFK(self::TABLE_NAME, Product::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
