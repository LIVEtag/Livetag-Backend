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
    const EXTERNAL_ID_SHOP_ID_INDEX = 'idx_unique_externalId_shopId';
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'externalId' => $this->string(255)->unsigned(),
                'shopId' => $this->integer()->unsigned(),
                'title' => $this->string(255)->notNull(),
                'photo'  => $this->string(255),
                'link' => $this->string(255),
                'status' => $this->tinyInteger(),
                'options' => $this->json()->notNull(),
                'createdAt' => $this->unixTimestamp(),
                'updatedAt' => $this->unixTimestamp(),
            ]
        );
        
        $this->addFK(self::TABLE_NAME, 'shopId', Shop::TABLE_NAME, 'id', 'CASCADE');
        $this->createIndex(self::EXTERNAL_ID_SHOP_ID_INDEX, self::TABLE_NAME, ['externalId', 'shopId'], true);
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, Shop::TABLE_NAME);
        $this->dropIndex(self::EXTERNAL_ID_SHOP_ID_INDEX, self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
