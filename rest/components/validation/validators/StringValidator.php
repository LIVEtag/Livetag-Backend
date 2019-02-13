<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\StringValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class StringValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::STRING_INVALID);
        }
        if ($this->tooShort === null) {
            $this->tooShort = $this->errorList->createErrorMessage(ErrorList::STRING_TOO_SHORT);
        }
        if ($this->tooLong === null) {
            $this->tooLong = $this->errorList->createErrorMessage(ErrorList::STRING_TOO_LONG);
        }
        if ($this->notEqual === null) {
            $this->notEqual = $this->errorList->createErrorMessage(ErrorList::STRING_NOT_EQUAL);
        }
        parent::init();
    }
}
