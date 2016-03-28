<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\db;

use yii\db\ColumnSchemaBuilder;

/**
 * Class ColumnSchemaBuilderPk
 */
class ColumnSchemaBuilderPk extends ColumnSchemaBuilder
{
    /**
     * Builds the length part of the column
     *
     * @return string
     */
    protected function buildLengthString()
    {
        if (!is_numeric($this->length)) {
            return '';
        }

        return sprintf('(%d)', $this->length);
    }

    /**
     * Builds the unsigned string for column
     *
     * @return string
     */
    protected function buildUnsignedString()
    {
        return $this->isUnsigned ? ' UNSIGNED' : '';
    }

    /**
     * Build full string for create the column's schema
     *
     * @return string
     */
    public function __toString()
    {
        return
            $this->type .
            $this->buildLengthString() .
            $this->buildUnsignedString() .
            ' NOT NULL' .
            ' AUTO_INCREMENT' .
            ' PRIMARY KEY';
    }
}
