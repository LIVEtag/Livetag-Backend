<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\validation\validators;

use yii\helpers\HtmlPurifier;
use yii\validators\Validator;

/**
 * Delete html tags
 */
class PurifyFilter extends Validator
{

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->filter = function ($value) {
            return HtmlPurifier::process($value, ['HTML.Allowed' => ""]);
        };
        parent::init();
    }
}
