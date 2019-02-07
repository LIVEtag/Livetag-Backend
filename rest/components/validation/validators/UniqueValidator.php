<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\UniqueValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class UniqueValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            if (is_array($this->targetAttribute) && count($this->targetAttribute) > 1) {
                $this->message = $this->errorList->createErrorMessage(ErrorList::UNIQUE_COMBO_INVALID);
            } else {
                $this->message = $this->errorList->createErrorMessage(ErrorList::UNIQUE_INVALID);
            }
        }
        parent::init();
    }
}
