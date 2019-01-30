<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\StringValidator as BaseValidator;
use rest\components\validation\ErrorsList;
use rest\components\validation\ErrorFormatterTrait;

class StringValidator extends BaseValidator
{
    use ErrorFormatterTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        /** @var ErrorsList $errorList */
        $errorList = \Yii::createObject(ErrorsList::class);

        if ($this->message === null) {
            $this->message = $errorList->createMessage(ErrorsList::ERR_STRING);
        }
        if ($this->tooShort === null) {
            $this->tooShort = $errorList->createMessage(ErrorsList::ERR_STRING_TOO_SHORT);
        }
        if ($this->tooLong === null) {
            $this->tooLong = $errorList->createMessage(ErrorsList::ERR_STRING_TOO_LONG);
        }
        if ($this->notEqual === null) {
            $this->notEqual = $errorList->createMessage(ErrorsList::ERR_STRING_NOT_EQUAL);
        }
        parent::init();
    }
}