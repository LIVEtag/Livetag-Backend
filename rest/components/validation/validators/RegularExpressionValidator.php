<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\RegularExpressionValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class RegularExpressionValidator extends BaseValidator
{
    use ValidationErrorTrait;

    public function init()
    {
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::REGULAR_EXPRESSION_INVALID);
        }
        parent::init();
    }
}
