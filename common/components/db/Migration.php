<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\db;

use yii\db\Migration as BaseMigration;

/**
 * Class Migration
 */
class Migration extends BaseMigration
{
    /**
     * http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
     */
    const TABLE_OPTIONS = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    /**
     * @param int $length
     * @return ColumnSchemaBuilderPk
     */
    public function primaryKey($length = null)
    {
        return new ColumnSchemaBuilderPk('int', $length ?: '11');
    }

    /**
     * @param int $length
     * @return ColumnSchemaBuilderPk
     */
    public function bigPrimaryKey($length = null)
    {
        return new ColumnSchemaBuilderPk('bigint', $length ?: '20');
    }

    /**
     * @param int $length
     * @return ColumnSchemaBuilderPk
     */
    public function smallPrimaryKey($length = null)
    {
        return new ColumnSchemaBuilderPk('smallint', $length ?: '5');
    }
}
