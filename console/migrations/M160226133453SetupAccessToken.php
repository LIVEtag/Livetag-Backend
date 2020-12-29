<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M130524201442SetupUser as User;

/**
 * Class M160226133453SetupAccessToken
 */
class M160226133453SetupAccessToken extends Migration
{
    const TABLE_NAME = '{{%access_token}}';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned(),
                'userId' => $this->integer()->unsigned()->notNull(),
                'token' => $this->string(128)->notNull(),
                // @link http://stackoverflow.com/a/20473371
                'userIp' => $this->string(46)->notNull()->defaultValue(''),
                // @link http://stackoverflow.com/a/20746656
                'userAgent' => $this->text(),
                'createdAt' => $this->unixTimestamp(),
                'expiredAt' => $this->unixTimestamp(),
            ]
        );

        $this->addFK(self::TABLE_NAME, 'userId', User::TABLE_NAME, 'id', 'CASCADE');
        $this->createIndex('idx_user_token', self::TABLE_NAME, 'token');
        $this->createIndex('idx_user_token_expired', self::TABLE_NAME, ['token', 'expiredAt']);
    }

    public function down()
    {
        $this->dropIndex('idx_user_token', self::TABLE_NAME);
        $this->dropIndex('idx_user_token_expired', self::TABLE_NAME);
        $this->dropFK(self::TABLE_NAME, User::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
