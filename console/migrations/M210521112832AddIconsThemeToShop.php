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
            $this->enum([
                'white',
                'light-gray',
                'gray',
                'dark-gray'
            ])->notNull()->defaultValue('white')->after('logo')
        );

        $this->addColumn(
            Shop::TABLE_NAME,
            'productIcon',
            $this->enum([
                'makeup',
                'clothes',
                'bags',
                'shoes',
                'cutlery',
                'food',
                'computers',
                'devices',
                'furniture',
                'decor',
                'lighting',
                'shopping'
            ])->notNull()->defaultValue('shopping')->after('iconsTheme')
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
