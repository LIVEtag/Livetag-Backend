<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\validation\validators;

use common\components\validation\ErrorMessage;
use yii\validators\FileValidator as BaseValidator;
use common\components\validation\ErrorList;
use common\components\validation\ValidationErrorTrait;
use yii\helpers\FileHelper;

class FileValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * @inheritdoc
     * @phpcsSuppress Generic.Files.LineLength.MaxExceeded
     */
    public function init()
    {
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
