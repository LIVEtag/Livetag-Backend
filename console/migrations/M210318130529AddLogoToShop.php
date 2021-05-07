<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M201229133425CreateShopTable as Shop;

/**
 * Class M210318130529AddLogoToShop
 */
class M210318130529AddLogoToShop extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Shop::TABLE_NAME, 'logo', $this->string()->after('website')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Shop::TABLE_NAME, 'logo');
    }
}
