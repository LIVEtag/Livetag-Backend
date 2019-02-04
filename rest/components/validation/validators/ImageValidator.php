<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\ImageValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ErrorMessage;
use rest\components\validation\ValidationErrorTrait;
use yii\helpers\FileHelper;

class ImageValidator extends BaseValidator
{
    use ValidationErrorTrait;

    public function init()
    {
        // File validator
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::FILE_INVALID);
        }
        if ($this->uploadRequired === null) {
            $this->uploadRequired = $this->errorList->createErrorMessage(ErrorList::FILE_UPLOAD_REQUIRED);
        }
        if ($this->tooMany === null) {
            $this->tooMany = $this->errorList->createErrorMessage(ErrorList::FILE_TOO_MANY);
        }
        if ($this->tooFew === null) {
            $this->tooFew = $this->errorList->createErrorMessage(ErrorList::FILE_TOO_FEW);
        }
        if ($this->wrongExtension === null) {
            $this->wrongExtension = $this->errorList->createErrorMessage(ErrorList::FILE_WRONG_EXTENSION);
        }
        if ($this->tooBig === null) {
            $this->tooBig = $this->errorList->createErrorMessage(ErrorList::FILE_TOO_BIG);
        }
        if ($this->tooSmall === null) {
            $this->tooSmall = $this->errorList->createErrorMessage(ErrorList::FILE_TOO_SMALL);
        }
        if ($this->wrongMimeType === null) {
            $this->wrongMimeType = $this->errorList->createErrorMessage(ErrorList::FILE_WRONG_MIME_TYPE);
        }

        // Image validator
        if ($this->notImage === null) {
            $this->notImage = $this->errorList->createErrorMessage(ErrorList::IMAGE_INVALID);
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

    /**
     * @inheritdoc
     */
    protected function beforeFormatMessage(ErrorMessage $message, array &$params): void
    {
        if ($message->getCode() === ErrorList::FILE_WRONG_MIME_TYPE) {
            $extensions = [];
            foreach ($this->mimeTypes as $mimeType) {
                foreach (FileHelper::getExtensionsByMimeType($mimeType) as $extension) {
                    $extensions[] = $extension;
                }
            }
            $params['extensions'] = implode(', ', $extensions);
        }
    }
}