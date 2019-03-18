<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\components\rbac\data;

use yii\base\InvalidArgumentException;

/**
 * Class ParamsReader
 */
class ParamsReader
{
    /**
     * @param array $params
     * @return int
     */
    public static function readUserId(array $params): int
    {
        if (!isset($params['userId'])) {
            throw new InvalidArgumentException('Key `userId` does not exists.');
        }

        return (int) $params['userId'];
    }
}
