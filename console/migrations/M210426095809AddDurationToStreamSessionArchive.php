<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210414144537CreateStreamSessionArchiveTable as StreamSessionArchive;

/**
 * Class M210426095809AddDurationToStreamSessionArchive
 */
class M210426095809AddDurationToStreamSessionArchive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            StreamSessionArchive::TABLE_NAME,
            'duration',
            $this->smallInteger()->unsigned()->comment('Duration in seconds')->notNull(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(StreamSessionArchive::TABLE_NAME, 'duration');
    }
}
