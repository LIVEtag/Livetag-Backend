<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\api\exceptions;

use rest\components\validation\ErrorListInterface;
use rest\components\validation\ErrorMessage;
use yii\base\InvalidConfigException;

/**
 * Class AbstractOauthException
 * @package rest\components\api\exceptions
 */
abstract class AbstractOauthException extends InvalidConfigException implements ThirdPartyParser
{
    /**
     * @var int
     */
    public $code = 400;

    /**
     * This method parses error message and returns appropriate error code from rest\components\validation\ErrorList.
     *
     * @param string $message
     * @return int
     */
    abstract public function parseError(string $message): int;

    /**
     * This method returns error message.
     *
     * @param ErrorListInterface $errorList
     * @param int $code
     * @return string
     */
    public function getThirdPartyMessage(ErrorListInterface $errorList, int $code): string
    {
        return $errorList->createErrorMessage($code)->getMessage();
    }

    /**
     * This method return error message object.
     *
     * @param ErrorListInterface $errorList
     * @return ErrorMessage
     */
    public function getErrorMessageObject(ErrorListInterface $errorList): ErrorMessage
    {
        $errorListCode = $this->parseError($this->getMessage());
        $message = $errorList->createErrorMessage($errorListCode);

        return $message;
    }
}
