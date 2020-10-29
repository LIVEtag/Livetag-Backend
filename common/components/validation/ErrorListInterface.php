<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\validation;

use JsonSerializable;

/**
 * Interface ErrorsListInterface
 * @package common\components\validation
 */
interface ErrorListInterface extends JsonSerializable
{
    /**
     * Error code attaching to basic errors in string format
     */
    public const ERR_BASIC = 1000;

    /**
     * Factory method for creating message via error code
     * @param int $code
     * @return ErrorMessage
     */
    public function createErrorMessage(int $code): ErrorMessage;
}
