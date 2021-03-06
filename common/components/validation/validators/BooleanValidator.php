<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\validation\validators;

use yii\validators\BooleanValidator as BaseValidator;
use common\components\validation\ErrorList;
use common\components\validation\ValidationErrorTrait;

class BooleanValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::BOOLEAN_INVALID);
        }
        parent::init();
    }
}
