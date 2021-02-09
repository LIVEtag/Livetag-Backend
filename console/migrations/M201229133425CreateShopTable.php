<?php

namespace console\migrations;

use common\components\db\Migration;

/**
 * Class M201229133425CreateShopTable
 */
class M201229133425CreateShopTable extends Migration
{
    const TABLE_NAME = '{{%shop}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'name' => $this->string(50)->notNull(),
                'uri' => $this->string(50)->unique()->notNull(),
                'website' => $this->string()->notNull(),
                'createdAt' => $this->unixTimestamp(),
                'updatedAt' => $this->unixTimestamp(),
            ]
        );
    }

    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
