<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210111122050CreateProductTable as Product;

/**
 * Class M210507092310ProductDescription
 */
class M210507092310ProductDescription extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Product::TABLE_NAME, 'description', $this->string(255)->null()->after('title'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Product::TABLE_NAME, 'description');
    }
}
