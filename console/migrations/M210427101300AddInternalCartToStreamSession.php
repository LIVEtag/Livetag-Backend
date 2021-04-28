<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210105142620CreateStreamSessionTable as StreamSession;

/**
 * Class M210427101300AddInternalCartToStreamSession
 */
class M210427101300AddInternalCartToStreamSession extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            StreamSession::TABLE_NAME,
            'internalCart',
            $this->boolean()->notNull()->defaultValue(false)->after('commentsEnabled')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(StreamSession::TABLE_NAME, 'internalCart');
    }
}
