<?php
namespace rest\components\validation;

class ErrorList implements ErrorListInterface
{
    public const STRING_ONLY = 1001;
    public const STRING_TOO_SHORT = 1002;
    public const STRING_TOO_LONG = 1003;
    public const STRING_NOT_EQUAL = 1004;

    public const EMAIL_ONLY = 1005;

    public const IMAGE_ONLY = 1006;
    public const IMAGE_UNDER_WIDTH = 1007;
    public const IMAGE_UNDER_HEIGHT = 1008;
    public const IMAGE_OVER_WIDTH = 1009;
    public const IMAGE_OVER_HEIGHT = 1010;

    protected const ERRORS = [
        self::STRING_ONLY => '{attr} must be a string.',
        self::STRING_TOO_SHORT => '{attr} should contain at least {min} character(s).',
        self::STRING_TOO_LONG => '{attr} should contain at most {max} character(s).',
        self::STRING_NOT_EQUAL => '{attr} should contain {length} character(s).',

        self::EMAIL_ONLY => '{attr} is not a valid email address.',

        self::IMAGE_ONLY => 'The file "{file}" is not an image.',
        self::IMAGE_UNDER_WIDTH => 'The image "{file}" is too small. The width cannot be smaller than {limit} pixel(s).',
        self::IMAGE_UNDER_HEIGHT => 'The image "{file}" is too small. The height cannot be smaller than {limit} pixel(s).',
        self::IMAGE_OVER_WIDTH => 'The image "{file}" is too large. The width cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        self::IMAGE_OVER_HEIGHT => 'The image "{file}" is too large. The height cannot be larger than {limit} pixel(s).',
    ];

    /**
     * Get error by code
     * @param int $code
     * @return string
     */
    protected function get(int $code): string
    {
        return static::ERRORS[$code];
    }

    /**
     * @inheritdoc
     */
    public function createErrorMessage(int $code): ErrorMessage
    {
        return new ErrorMessage($this->get($code), $code);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return static::ERRORS;
    }
}