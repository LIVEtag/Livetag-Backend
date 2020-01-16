<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace console\migrations;

use common\components\db\Migration;

/**
 * Class M160226133453SetupAccessToken
 */
class M160226133453SetupAccessToken extends Migration
{

    public function up()
    {
        $this->createTable(
            '{{%access_token}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'userId' => $this->integer()->unsigned()->notNull(),
                'token' => $this->string(128)->notNull(),
                // @link http://stackoverflow.com/a/20473371
                'userIp' => $this->string(46)->notNull()->defaultValue(''),
                // @link http://stackoverflow.com/a/20746656
                'userAgent' => $this->text(),
                'createdAt' => $this->integer()->unsigned()->notNull(),
                'expiredAt' => $this->integer()->unsigned()->notNull(),
            ]
        );

        $this->addForeignKey(
            'fk_access_token_to_user',
            '{{%access_token}}',
            'userId',
            '{{%user}}',
            'id',
            'CASCADE'
        );
        $this->createIndex('idx_user_token', '{{%access_token}}', 'token');
        $this->createIndex('idx_user_token_expired', '{{%access_token}}', ['token', 'expiredAt']);
    }

    public function down()
    {
        $this->dropIndex('idx_user_token', '{{%access_token}}');
        $this->dropIndex('idx_user_token_expired', '{{%access_token}}');
        $this->dropForeignKey('fk_access_token_to_user', '{{%access_token}}');
        $this->dropTable('{{%access_token}}');
    }
}
