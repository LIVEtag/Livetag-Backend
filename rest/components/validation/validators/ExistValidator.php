<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\ExistValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class ExistValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::EXIST_INVALID);
        }
        parent::init();
    }
}
