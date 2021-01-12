<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\validation\validators;

use common\components\validation\ErrorList;
use common\components\validation\ValidationErrorTrait;
use yii\validators\RegularExpressionValidator;

/**
 * PasswordValidator validates that password match pattern
 */
class PasswordValidator extends RegularExpressionValidator
{
    use ValidationErrorTrait;

    /**
     * @var string the regular expression to be matched with
     */
    public $pattern = '/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::PASSWORD_FORMAT);
        }
        parent::init();
    }
}
