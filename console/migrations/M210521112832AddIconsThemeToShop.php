<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M201229133425CreateShopTable as Shop;

/**
 * Class M210521112832AddIconsThemeToShop
 */
class M210521112832AddIconsThemeToShop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            Shop::TABLE_NAME,
            'iconsTheme',
            $this->string(15)->notNull()->defaultValue('white')->after('logo')
        );

        $this->addColumn(
            Shop::TABLE_NAME,
            'productIcon',
            $this->string(15)->notNull()->defaultValue('shopping')->after('logo')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Shop::TABLE_NAME, 'iconsTheme');
        $this->dropColumn(Shop::TABLE_NAME, 'productIcon');
    }
}
