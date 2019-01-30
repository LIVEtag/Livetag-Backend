<?php
namespace rest\components\validation;

use JsonSerializable;

class ErrorsList implements JsonSerializable
{
    public const ERR_BASIC = 1000;
    public const ERR_STRING = 1001;
    public const ERR_STRING_TOO_SHORT = 1002;
    public const ERR_STRING_TOO_LONG = 1003;
    public const ERR_STRING_NOT_EQUAL = 1004;

    public const ERRORS = [
        self::ERR_STRING => '{attr} must be a string.',

        self::ERR_STRING_TOO_SHORT => '{attr} should contain at least {min} character(s).',
        self::ERR_STRING_TOO_LONG => '{attr} should contain at most {max} character(s).',
        self::ERR_STRING_NOT_EQUAL => '{attr} should contain {length} character(s).',
    ];

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return static::ERRORS;
    }

    /**
     * Factory method for creating message via error code
     * @param int $code
     * @return ErrorMessage
     */
    public function createMessage(int $code): ErrorMessage
    {
        return new ErrorMessage(static::ERRORS[$code], $code);
    }
}