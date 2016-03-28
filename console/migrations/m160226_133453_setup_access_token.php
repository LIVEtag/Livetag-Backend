<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\db\Migration;
use rest\common\models\AccessToken;
use rest\common\models\User;

/**
 * Class m160226_133453_setup_access_token
 */
class m160226_133453_setup_access_token extends Migration
{
    public function up()
    {
        $this->createTable(
            AccessToken::tableName(),
            [
                'id' => $this->primaryKey()->unsigned(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'token' => $this->string(128)->notNull(),
                // @link http://stackoverflow.com/a/20473371
                'user_ip' => $this->string(46)->notNull()->defaultValue(''),
                // @link http://stackoverflow.com/a/20746656
                'user_agent' => $this->text(),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'expired_at' => $this->integer()->unsigned()->notNull(),
            ],
            self::TABLE_OPTIONS
        );

        $this->addForeignKey(
            'fk_access_token_to_user',
            AccessToken::tableName(),
            'user_id',
            User::tableName(),
            'id',
            'CASCADE'
        );
        $this->createIndex('idx_user_token', AccessToken::tableName(), 'token');
        $this->createIndex('idx_user_token_expired', AccessToken::tableName(), ['token', 'expired_at']);
    }

    public function down()
    {
        $this->dropIndex('idx_user_token', AccessToken::tableName());
        $this->dropIndex('idx_user_token_expired', AccessToken::tableName());
        $this->dropForeignKey('fk_access_token_to_user', AccessToken::tableName());
        $this->dropTable(AccessToken::tableName());
    }
}
