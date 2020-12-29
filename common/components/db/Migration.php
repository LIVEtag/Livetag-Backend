<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\db;

use yii\base\NotSupportedException;
use yii\db\ColumnSchemaBuilder;
use yii\db\Migration as BaseMigration;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * Class Migration
 */
class Migration extends BaseMigration
{
    /**
     * @see https://dev.mysql.com/doc/refman/5.5/en/charset-unicode-utf8mb4.html
     */
    const TABLE_OPTIONS = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

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

    /**
     * @return ColumnSchemaBuilder
     */
    public function unixTimestamp()
    {
        return $this->integer()->unsigned()->notNull();
    }

    /**
     * addForeignKey with name generation based on table names
     * @see addForeignKey - add FK with auto name generation
     *
     * @param string $table the table that the foreign key constraint will be added to.
     * @param string|array $columns the name of the column to that the constraint will be added on.
     *  If there are multiple columns, separate them with commas or use an array.
     * @param string $refTable the table that the foreign key references to.
     * @param string|array $refColumns the name of the column that the foreign key references to.
     *  If there are multiple columns, separate them with commas or use an array.
     * @param string $delete the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
     * @param string $update the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
     */
    public function addFK($table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        return $this->addForeignKey($this->getFKName($table, $refTable), $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    /**
     * Builds a SQL statement for dropping a foreign key constraint.
     * @param string $table the table whose foreign is to be dropped. The name will be properly quoted by the method.
     * @param string $refTable the table that the foreign key references to.
     */
    public function dropFK($table, $refTable)
    {
        return $this->dropForeignKey($this->getFKName($table, $refTable), $table);
    }

    /**
     * Generate FK name between 2 tables
     * @param string $table the table whose foreign is.
     * @param string $refTable the table that the foreign key references to.
     * @return string
     */
    protected function getFKName($table, $refTable): string
    {
        return StringHelper::truncate(Inflector::slug('fk_' . $table . '_' . $refTable, '_'), 64, '');
    }
}
