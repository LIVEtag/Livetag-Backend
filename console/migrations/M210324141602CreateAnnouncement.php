<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;
use yii\db\Expression;

/**
 * Class M210324141602CreateAnnouncement
 */
class M210324141602CreateAnnouncement extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(StreamSession::TABLE_NAME, 'name', $this->string(55)->after('status')->notNull());

        $this->addColumn(
            StreamSession::TABLE_NAME,
            'announcedAt',
            $this->integer()->unsigned()->null()->after('createdAt')->comment('Announcement time')
        );
        $this->addColumn(
            StreamSession::TABLE_NAME,
            'duration',
            $this->smallInteger()->unsigned()->defaultValue(10800)->after('announcedAt')->comment('Duration in seconds (up to 3 hours)')
        );

        //update announcedAt to createdAt
        $this->update(StreamSession::TABLE_NAME, ['announcedAt' => new Expression('createdAt')]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(StreamSession::TABLE_NAME, 'duration');
        $this->dropColumn(StreamSession::TABLE_NAME, 'announcedAt');
        $this->dropColumn(StreamSession::TABLE_NAME, 'name');
    }
}
