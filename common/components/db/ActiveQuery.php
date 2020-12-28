<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\db;

class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * Return full field name with table prefix
     * @param string $attribute
     * @return string
     * @SuppressWarnings("UndefinedVariable")
     */
    public function getFieldName(string $attribute): string
    {
        [1 => $alias] = $this->getTableNameAndAlias();
        return "{$alias}.{$attribute}";
    }
}
