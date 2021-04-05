<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210405081924AddIsPublishedToStreamSession
 */
class M210405081924AddIsPublishedToStreamSession extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(StreamSession::TABLE_NAME, 'isPublished', $this->boolean()->notNull()->defaultValue(true));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(StreamSession::TABLE_NAME, 'isPublished');
    }
}
