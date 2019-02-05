<?php
namespace rest\components\validation;

class ErrorList implements ErrorListInterface
{
    public const EMAIL_INVALID = 1010;

    public const DATE_INVALID = 1020;
    public const DATE_TOO_SMALL = 1021;
    public const DATE_TOO_BIG = 1022;

    public const FILE_INVALID = 1030;
    public const FILE_UPLOAD_REQUIRED = 1031;
    public const FILE_TOO_MANY = 1032;
    public const FILE_TOO_FEW = 1033;
    public const FILE_WRONG_EXTENSION = 1034;
    public const FILE_TOO_BIG = 1035;
    public const FILE_TOO_SMALL = 1036;
    public const FILE_WRONG_MIME_TYPE = 1037;

    public const IMAGE_INVALID = 1040;
    public const IMAGE_UNDER_WIDTH = 1041;
    public const IMAGE_UNDER_HEIGHT = 1042;
    public const IMAGE_OVER_WIDTH = 1043;
    public const IMAGE_OVER_HEIGHT = 1044;

    public const NUMBER_INVALID = 1050;
    public const NUMBER_INTEGER_ONLY = 1051;
    public const NUMBER_TOO_SMALL = 1052;
    public const NUMBER_TOO_BIG = 1053;

    public const REQUIRED_INVALID = 1060;
    public const REQUIRED_VALUE = 1061;

    public const REGULAR_EXPRESSION_INVALID = 1070;

    public const STRING_INVALID = 1080;
    public const STRING_TOO_SHORT = 1081;
    public const STRING_TOO_LONG = 1082;
    public const STRING_NOT_EQUAL = 1083;



    public const BOOLEAN_INVALID = 1100;

    public const RANGE_INVALID = 1120;


    protected const ERRORS = [
        self::EMAIL_INVALID => '{attr} is not a valid email address.',

        self::DATE_INVALID => 'The format of {attr} is invalid.',
        self::DATE_TOO_SMALL => '{attr} must be no less than {min}.',
        self::DATE_TOO_BIG => '{attr} must be no greater than {max}.',

        self::FILE_INVALID => 'File upload failed.',
        self::FILE_UPLOAD_REQUIRED => 'Please upload a file.',
        self::FILE_TOO_MANY => 'You can upload at most {limit} file(s).',
        self::FILE_TOO_FEW => 'You should upload at least {limit} file(s).',
        self::FILE_WRONG_EXTENSION => 'Only files with these extensions are allowed: {extensions}.',
        self::FILE_TOO_BIG => 'The file "{file}" is too big. Its size cannot exceed {formattedLimit}.',
        self::FILE_TOO_SMALL => 'The file "{file}" is too small. Its size cannot be smaller than {formattedLimit}.',
        self::FILE_WRONG_MIME_TYPE => 'Only files with these MIME types are allowed: {mimeTypes}.',

        self::IMAGE_INVALID => 'The file "{file}" is not an image.',
        self::IMAGE_UNDER_WIDTH => 'The image "{file}" is too small. The width cannot be smaller than {limit} pixel(s).',
        self::IMAGE_UNDER_HEIGHT => 'The image "{file}" is too small. The height cannot be smaller than {limit} pixel(s).',
        self::IMAGE_OVER_WIDTH => 'The image "{file}" is too large. The width cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        self::IMAGE_OVER_HEIGHT => 'The image "{file}" is too large. The height cannot be larger than {limit} pixel(s).',

        self::NUMBER_INVALID => '{attr} must be a number.',
        self::NUMBER_INTEGER_ONLY => '{attr} must be an integer.',
        self::NUMBER_TOO_SMALL => '{attr} must be no less than {min}.',
        self::NUMBER_TOO_BIG => '{attr} must be no greater than {max}.',

        self::REQUIRED_INVALID => '{attr} cannot be blank.',
        self::REQUIRED_VALUE => '{attr} must be "{requiredValue}".',

        self::REGULAR_EXPRESSION_INVALID => '{attr} is invalid.',

        self::STRING_INVALID => '{attr} must be a string.',
        self::STRING_TOO_SHORT => '{attr} should contain at least {min} character(s).',
        self::STRING_TOO_LONG => '{attr} should contain at most {max} character(s).',
        self::STRING_NOT_EQUAL => '{attr} should contain {length} character(s).',


        self::BOOLEAN_INVALID => '{attr} must be either "{true}" or "{false}"',

        self::RANGE_INVALID => '{attr} is not allowed.',
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