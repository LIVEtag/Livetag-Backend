<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use rest\components\validation\ErrorMessage;
use yii\validators\CompareValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class CompareValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            switch ($this->operator) {
                case '==':
                case '===':
                    $this->message = $this->errorList->createErrorMessage(ErrorList::COMPARE_EQUAL);
                    break;
                case '!=':
                case '!==':
                    $this->message = $this->errorList->createErrorMessage(ErrorList::COMPARE_NOT_EQUAL);
                    break;
                case '>':
                    $this->message = $this->errorList->createErrorMessage(ErrorList::COMPARE_GREATER_THEN);
                    break;
                case '>=':
                    $this->message = $this->errorList->createErrorMessage(ErrorList::COMPARE_GREATER_OR_EQUAL);
                    break;
                case '<':
                    $this->message = $this->errorList->createErrorMessage(ErrorList::COMPARE_LESS_THEN);
                    break;
                case '<=':
                    $this->message = $this->errorList->createErrorMessage(ErrorList::COMPARE_LESS_OR_EQUAL);
                    break;
            }
        }
        parent::init();
    }

    /**
     * @param ErrorMessage $message
     * @param $params
     */
    protected function beforeFormatMessage(ErrorMessage $message, array &$params): void
    {
        $params = $this->renameArrayKeys($params, [
            'compareValueOrAttribute' => 'compareValueOrAttr',
            'compareAttribute' => 'compareAttr',
        ]);
    }
}
