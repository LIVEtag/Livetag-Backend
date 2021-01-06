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
                'name' => $this->string()->notNull(),
                'website' => $this->string()->notNull(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
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