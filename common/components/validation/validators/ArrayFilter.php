<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\validation\validators;

use yii\validators\FilterValidator;

/**
 * Filter for array inputs in API. Support arrays and comma-separated strings.
 */
class ArrayFilter extends FilterValidator
{

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->filter = function ($value) {
            if (!$value) {
                return [];
            }
            return is_array($value) ? $value : array_map('trim', explode(',', $value));
        };
        parent::init();
    }
}
