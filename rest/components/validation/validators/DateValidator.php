<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\DateValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class DateValidator extends BaseValidator
{
    use ValidationErrorTrait;

    public function init()
    {
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::DATE_INVALID);
        }
        if ($this->tooSmall === null) {
            $this->tooSmall = $this->errorList->createErrorMessage(ErrorList::DATE_TOO_SMALL);
        }
        if ($this->tooBig === null) {
            $this->tooBig = $this->errorList->createErrorMessage(ErrorList::DATE_TOO_BIG);
        }
        parent::init();
    }
}
