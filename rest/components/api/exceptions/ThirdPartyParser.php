<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\api\exceptions;

use rest\components\validation\ErrorListInterface;
use rest\components\validation\ErrorMessage;

/**
 * Interface ThirdPartyParser
 * @package rest\components\api\exceptions
 */
interface ThirdPartyParser
{
    /**
     * This method parses error message and returns appropriate error code from rest\components\validation\ErrorList.
     *
     * @param string $message
     * @return int
     */
    public function parseError(string $message): int;

    /**
     * This method returns error message.
     *
     * @param ErrorListInterface $errorList
     * @param int $code
     * @return string
     */
    public function getThirdPartyMessage(ErrorListInterface $errorList, int $code): string;

    /**
     * This method return error message object.
     *
     * @param ErrorListInterface $errorList
     * @return ErrorMessage
     */
    public function getErrorMessageObject(ErrorListInterface $errorList): ErrorMessage;
}
