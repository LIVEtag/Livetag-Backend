<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M130524201442SetupUser as User;
use console\migrations\M201229133425CreateShopTable as Shop;

/**
 * Class M201229134027CreateUserShopTable
 */
class M201229134027CreateUserShopTable extends Migration
{
    const TABLE_NAME = '{{%user_shop}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'userId' => $this->integer()->unsigned()->notNull(),
                'shopId' => $this->integer()->unsigned()->notNull(),
            ]
        );
        $this->addPrimaryKey('PK_user_shop', self::TABLE_NAME, ['userId', 'shopId']);
        $this->addFK(self::TABLE_NAME, 'userId', User::TABLE_NAME, 'id', 'CASCADE');
        $this->addFK(self::TABLE_NAME, 'shopId', Shop::TABLE_NAME, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropFK(self::TABLE_NAME, Shop::TABLE_NAME);
        $this->dropFK(self::TABLE_NAME, User::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
