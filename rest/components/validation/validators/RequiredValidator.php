<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\RequiredValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class RequiredValidator extends BaseValidator
{
    use ValidationErrorTrait;

    public function init()
    {
        if ($this->message === null) {
            if ($this->requiredValue === null) {
                $this->message = $this->errorList->createErrorMessage(ErrorList::REQUIRED_INVALID);
            } else {
                $this->message = $this->errorList->createErrorMessage(ErrorList::REQUIRED_VALUE);
            }
        }
        parent::init();
    }
}
