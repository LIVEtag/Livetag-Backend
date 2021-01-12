<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M201229133425CreateShopTable as Shop;

/**
 * Class M210111122050CreateProductTable
 */
class M210111122050CreateProductTable extends Migration
{
    const TABLE_NAME = '{{%product}}';
    
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'externalId' => $this->string(255)->unsigned()->unique()->notNull(),
                'shopId' => $this->integer()->unsigned(),
                'title' => $this->string(255)->notNull(),
                'options' => $this->json()->null(),
                'photo'  => $this->string(255),
                'link' => $this->string(255),
                'status' => $this->tinyInteger(),
                'createdAt' => $this->unixTimestamp(),
                'updatedAt' => $this->unixTimestamp(),
            ]
        );
        
        $this->addFK(self::TABLE_NAME, 'shopId', Shop::TABLE_NAME, 'id', 'CASCADE');
    
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, Shop::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
