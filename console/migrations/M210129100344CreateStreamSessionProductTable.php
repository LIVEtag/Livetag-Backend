<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;
use console\migrations\M210111122050CreateProductTable as Product;

/**
 * Class M210129100344CreateStreamSessionProductTable
 */
class M210129100344CreateStreamSessionProductTable extends Migration
{
    const TABLE_NAME = '{{%stream_session_product}}';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'streamSessionId' => $this->integer()->unsigned(),
                'productId' => $this->integer()->unsigned(),
                'status' => $this->tinyInteger(),
                'createdAt' => $this->unixTimestamp(),
                'updatedAt' => $this->unixTimestamp(),
            ]
        );
        $this->createIndex('uk_session_product', self::TABLE_NAME, ['streamSessionId', 'productId'], true);
        $this->addFK(self::TABLE_NAME, 'streamSessionId', StreamSession::TABLE_NAME, 'id', 'CASCADE');
        $this->addFK(self::TABLE_NAME, 'productId', Product::TABLE_NAME, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, Product::TABLE_NAME);
        $this->dropFK(self::TABLE_NAME, StreamSession::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
