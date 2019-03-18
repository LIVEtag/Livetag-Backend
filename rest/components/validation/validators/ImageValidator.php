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

    /**
     * @inheritdoc
     */
    public function init()
    {
        // File validator
        $this->message = $this->message ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_INVALID);
        $this->uploadRequired = $this->uploadRequired ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_UPLOAD_REQUIRED);
        $this->tooMany = $this->tooMany ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_TOO_MANY);
        $this->tooFew = $this->tooFew ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_TOO_FEW);
        $this->wrongExtension = $this->wrongExtension ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_WRONG_EXTENSION);
        $this->tooBig = $this->tooBig ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_TOO_BIG);
        $this->tooSmall = $this->tooSmall ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_TOO_SMALL);
        $this->wrongMimeType = $this->wrongMimeType ?? $this->errorList
                ->createErrorMessage(ErrorList::FILE_WRONG_MIME_TYPE);

        // Image validator
        $this->notImage = $this->notImage ?? $this->errorList->
            createErrorMessage(ErrorList::IMAGE_INVALID);
        $this->underWidth = $this->underWidth ?? $this->errorList
                ->createErrorMessage(ErrorList::IMAGE_UNDER_WIDTH);
        $this->underHeight = $this->underHeight ?? $this->errorList
                ->createErrorMessage(ErrorList::IMAGE_UNDER_HEIGHT);
        $this->overWidth = $this->overWidth ?? $this->errorList
                ->createErrorMessage(ErrorList::IMAGE_OVER_WIDTH);
        $this->overHeight = $this->overHeight ?? $this->errorList
                ->createErrorMessage(ErrorList::IMAGE_OVER_HEIGHT);
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
