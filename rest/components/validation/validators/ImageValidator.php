<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;
use yii\validators\ImageValidator as BaseValidator;

class ImageValidator extends BaseValidator
{
    use ValidationErrorTrait;

    public function init()
    {
        if ($this->notImage === null) {
            $this->notImage = $this->errorList->createErrorMessage(ErrorList::IMAGE_ONLY);
        }
        if ($this->underWidth === null) {
            $this->underWidth = $this->errorList->createErrorMessage(ErrorList::IMAGE_UNDER_WIDTH);
        }
        if ($this->underHeight === null) {
            $this->underHeight = $this->errorList->createErrorMessage(ErrorList::IMAGE_UNDER_HEIGHT);
        }
        if ($this->overWidth === null) {
            $this->overWidth = $this->errorList->createErrorMessage(ErrorList::IMAGE_OVER_WIDTH);
        }
        if ($this->overHeight === null) {
            $this->overHeight = $this->errorList->createErrorMessage(ErrorList::IMAGE_OVER_HEIGHT);
        }
        parent::init();
    }
}