<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\NumberValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;
use rest\components\validation\ErrorMessage;

class NumberValidator extends BaseValidator
{
    use ValidationErrorTrait;
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            if ($this->integerOnly) {
                $this->message = $this->errorList->createErrorMessage(ErrorList::NUMBER_INTEGER_ONLY);
            } else {
                $this->message = $this->errorList->createErrorMessage(ErrorList::NUMBER_INVALID);
            }
        }
        if ($this->tooSmall === null) {
            $this->tooSmall = $this->errorList->createErrorMessage(ErrorList::NUMBER_TOO_SMALL);
        }
        if ($this->tooBig === null) {
            $this->tooBig = $this->errorList->createErrorMessage(ErrorList::NUMBER_TOO_BIG);
        }
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        $result = parent::validateValue($value);
        if (isset($result[0]) && !($result[0] instanceof ErrorMessage)) {
            $result[0] = $this->message;
        }
        return $result;
    }
}
