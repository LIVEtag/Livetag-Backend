<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\helpers;

use yii\helpers\ArrayHelper;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
class ExpandHelper
{
    /**
     * @see yii\rest\Serializer expandParam
     */
    const EXPAND_PARAM = 'expand';

    /**
     * Extract first level expand only
     * @see yii\rest\Serializer getRequestedFields()
     * @param array $params
     * @return array
     */
    public static function getExpand(array $params): array
    {
        $expandString = ArrayHelper::getValue($params, self::EXPAND_PARAM);
        $expand = is_string($expandString) ? preg_split('/\s*,\s*/', $expandString, -1, PREG_SPLIT_NO_EMPTY) : [];
        return self::extractRootFields($expand);
    }

    /**
     * @see yii\base\ArrayableTrait extractRootFields()
     */
    public static function extractRootFields(array $fields): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result[] = current(explode('.', $field, 2));
        }
        if (in_array('*', $result, true)) {
            $result = [];
        }
        return array_unique($result);
    }
}
