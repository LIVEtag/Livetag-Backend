<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\db;

use yii\base\NotSupportedException;
use yii\db\ColumnSchemaBuilder;
use yii\db\Migration as BaseMigration;

/**
 * Class Migration
 */
class Migration extends BaseMigration
{
    /**
     * @see https://dev.mysql.com/doc/refman/5.5/en/charset-unicode-utf8mb4.html
     */
    public const TABLE_OPTIONS = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

    protected const DRIVERS_MYSQL = [
        'mysql', 'mysqli'
    ];

    /**
     * @inheritDoc
     */
    public function createTable($table, $columns, $options = null)
    {
        if ($options === null) {
            $options = self::TABLE_OPTIONS;
        }
        parent::createTable($table, $columns, $options);
    }

    /**
     * Check if method supported by driver
     * @param string|string[] $driver
     * @throws NotSupportedException
     */
    protected function checkDriverSupport($driver)
    {
        if (!in_array($this->db->getDriverName(), (array) $driver, true)) {
            throw new NotSupportedException("Method not supported by {$this->db->getDriverName()}");
        }
    }

    /**
     * Creates a MySql medium text column
     * @return ColumnSchemaBuilder
     */
    public function mediumText()
    {
        $this->checkDriverSupport(self::DRIVERS_MYSQL);
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext');
    }

    /**
     * Creates a long text column.
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function longText()
    {
        $this->checkDriverSupport(self::DRIVERS_MYSQL);
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext');
    }

    /**
     * Create MySql enum column
     * @param array $values
     * @return ColumnSchemaBuilder
     */
    public function enum(array $values)
    {
        $this->checkDriverSupport(self::DRIVERS_MYSQL);
        $values = implode(
            ', ',
            array_map(
                static function ($value) {
                    return "'$value'";
                },
                $values
            )
        );
        return $this->getDb()->getSchema()->createColumnSchemaBuilder("ENUM({$values})");
    }
}
