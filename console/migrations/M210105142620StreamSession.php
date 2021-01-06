<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M201229133425CreateShopTable as Shop;

/**
 * Class M210105142620StreamSession
 */
class M210105142620StreamSession extends Migration
{
    const TABLE_NAME = '{{%stream_session}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'shopId' => $this->integer()->unsigned()->notNull(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'sessionId' => $this->string(255)->notNull(), //A session ID string can be up to 255 characters long.
                'publisherToken' => $this->string(512)->notNull(),
                'expiredAt' => $this->unixTimestamp()->comment('Publisher token expiration time'),
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
