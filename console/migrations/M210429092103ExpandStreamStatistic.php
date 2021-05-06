<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210216105937CreateStreamSessionStatisticTable as StreamSessionStatistic;
use yii\db\Expression;

/**
 * Class M210429092103ExpandStreamStatistic
 */
class M210429092103ExpandStreamStatistic extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropIndex('idx_addToCartCount', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_viewsCount', StreamSessionStatistic::TABLE_NAME);

        $this->renameColumn(StreamSessionStatistic::TABLE_NAME, 'addToCartCount', 'totalAddToCartCount');
        $this->renameColumn(StreamSessionStatistic::TABLE_NAME, 'viewsCount', 'totalViewCount');
        $this->addColumn(StreamSessionStatistic::TABLE_NAME, 'totalAddToCartRate', $this->decimal(11, 2)->unsigned()->defaultValue(0));

        $this->addColumn(StreamSessionStatistic::TABLE_NAME, 'streamAddToCartCount', $this->integer()->unsigned()->defaultValue(0));
        $this->addColumn(StreamSessionStatistic::TABLE_NAME, 'streamViewCount', $this->integer()->unsigned()->defaultValue(0));
        $this->addColumn(StreamSessionStatistic::TABLE_NAME, 'streamAddToCartRate', $this->decimal(11, 2)->unsigned()->defaultValue(0));

        $this->addColumn(StreamSessionStatistic::TABLE_NAME, 'archiveAddToCartCount', $this->integer()->unsigned()->defaultValue(0));
        $this->addColumn(StreamSessionStatistic::TABLE_NAME, 'archiveViewCount', $this->integer()->unsigned()->defaultValue(0));
        $this->addColumn(StreamSessionStatistic::TABLE_NAME, 'archiveAddToCartRate', $this->decimal(11, 2)->unsigned()->defaultValue(0));



        $this->createIndex('idx_totalAddToCartCount', StreamSessionStatistic::TABLE_NAME, 'totalAddToCartCount');
        $this->createIndex('idx_totalViewCount', StreamSessionStatistic::TABLE_NAME, 'totalViewCount');
        $this->createIndex('idx_totalAddToCartRate', StreamSessionStatistic::TABLE_NAME, 'totalAddToCartRate');
        $this->createIndex('idx_streamAddToCartCount', StreamSessionStatistic::TABLE_NAME, 'streamAddToCartCount');
        $this->createIndex('idx_streamViewCount', StreamSessionStatistic::TABLE_NAME, 'streamViewCount');
        $this->createIndex('idx_streamAddToCartRate', StreamSessionStatistic::TABLE_NAME, 'streamAddToCartRate');
        $this->createIndex('idx_archiveAddToCartCount', StreamSessionStatistic::TABLE_NAME, 'archiveAddToCartCount');
        $this->createIndex('idx_archiveViewCount', StreamSessionStatistic::TABLE_NAME, 'archiveViewCount');
        $this->createIndex('idx_archiveAddToCartRate', StreamSessionStatistic::TABLE_NAME, 'archiveAddToCartRate');


        // calculate total rate
        $addToCartRateExpression = new Expression('COALESCE(totalAddToCartCount/NULLIF(totalViewCount,0),0)');
        $this->update(StreamSessionStatistic::TABLE_NAME, ['totalAddToCartRate' => $addToCartRateExpression]);

        // copy values from total to live
        $this->update(StreamSessionStatistic::TABLE_NAME, ['streamAddToCartCount' => new Expression('totalAddToCartCount')]);
        $this->update(StreamSessionStatistic::TABLE_NAME, ['streamViewCount' => new Expression('totalViewCount')]);
        $this->update(StreamSessionStatistic::TABLE_NAME, ['streamAddToCartRate' => new Expression('totalAddToCartRate')]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_totalAddToCartCount', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_totalViewCount', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_totalAddToCartRate', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_streamAddToCartCount', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_streamViewCount', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_streamAddToCartRate', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_archiveAddToCartCount', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_archiveViewCount', StreamSessionStatistic::TABLE_NAME);
        $this->dropIndex('idx_archiveAddToCartRate', StreamSessionStatistic::TABLE_NAME);


        $this->renameColumn(StreamSessionStatistic::TABLE_NAME, 'totalAddToCartCount', 'addToCartCount');
        $this->renameColumn(StreamSessionStatistic::TABLE_NAME, 'totalViewCount', 'viewsCount');
        $this->dropColumn(StreamSessionStatistic::TABLE_NAME, 'totalAddToCartRate');

        $this->dropColumn(StreamSessionStatistic::TABLE_NAME, 'streamAddToCartCount');
        $this->dropColumn(StreamSessionStatistic::TABLE_NAME, 'streamViewCount');
        $this->dropColumn(StreamSessionStatistic::TABLE_NAME, 'streamAddToCartRate');

        $this->dropColumn(StreamSessionStatistic::TABLE_NAME, 'archiveAddToCartCount');
        $this->dropColumn(StreamSessionStatistic::TABLE_NAME, 'archiveViewCount');
        $this->dropColumn(StreamSessionStatistic::TABLE_NAME, 'archiveAddToCartRate');


        $this->createIndex('idx_addToCartCount', StreamSessionStatistic::TABLE_NAME, 'addToCartCount');
        $this->createIndex('idx_viewsCount', StreamSessionStatistic::TABLE_NAME, 'viewsCount');
    }
}
