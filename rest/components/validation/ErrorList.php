<?php
namespace rest\components\validation;

/**
 * Class ErrorList
 * @package rest\components\validation
 * @codingStandardsIgnoreFile Generic.Files.LineLength.MaxExceeded
 */
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

    public const URL_INVALID = 1090;

    public const BOOLEAN_INVALID = 1100;

    public const COMPARE_EQUAL = 1110;
    public const COMPARE_NOT_EQUAL = 1111;
    public const COMPARE_GREATER_THEN = 1112;
    public const COMPARE_GREATER_OR_EQUAL = 1113;
    public const COMPARE_LESS_THEN = 1114;
    public const COMPARE_LESS_OR_EQUAL = 1115;

    public const IN_INVALID = 1120;

    public const IP_INVALID = 1130;
    public const IP_V6_NOT_ALLOWED = 1131;
    public const IP_V4_NOT_ALLOWED = 1132;
    public const IP_WRONG_CIDR = 1133;
    public const IP_NO_SUBNET = 1134;
    public const IP_HAS_SUBNET = 1135;
    public const IP_NOT_IN_RANGE = 1136;

    public const UNIQUE_INVALID = 1150;
    public const UNIQUE_COMBO_INVALID = 1151;

    public const EXIST_INVALID = 1160;

    // Custom errors
    public const CAPTCHA_INVALID = 1140;
    public const CREDENTIALS_INVALID = 1200;
    public const ENTITY_BLOCKED = 1210;
    public const CURRENT_PASSWORD_IS_WRONG = 1220;
    public const SAME_CURRENT_PASSWORD_AND_NEW_PASSWORD = 1230;
    public const USER_NOT_FOUND = 1231;

    public const THIRD_PARTY_NOT_DOCUMENTED_ERROR = 3000;

    //facebook oauth error
    public const FACEBOOK_INVALID_APP_SECRET = 3101;
    public const FACEBOOK_ERROR_TOKEN_VALIDATION = 3102;
    public const FACEBOOK_INVALID_OAUTH_TOKEN = 3103;
    public const FACEBOOK_MALFORMED_ACCESS_TOKEN = 3104;

    //linkedin oauth error
    public const LINKEDIN_INVALID_OAUTH_TOKEN = 3203;

    //google oauth error
    public const GOOGLE_INVALID_OAUTH_TOKEN = 3303;

    //twitter oauth error
    public const TWITTER_INVALID_OAUTH_TOKEN = 3403;

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
        self::IMAGE_OVER_WIDTH => 'The image "{file}" is too large. The width cannot be larger than {limit} pixel(s).',
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

        self::URL_INVALID => '{attr} is not a valid link.',

        self::BOOLEAN_INVALID => '{attr} must be either "{true}" or "{false}"',

        self::COMPARE_EQUAL => '{attr} must be equal to "{compareValueOrAttr}".',
        self::COMPARE_NOT_EQUAL => '{attr} must not be equal to "{compareValueOrAttr}".',
        self::COMPARE_GREATER_THEN => '{attr} must be greater than "{compareValueOrAttr}".',
        self::COMPARE_GREATER_OR_EQUAL => '{attr} must be greater than or equal to "{compareValueOrAttr}".',
        self::COMPARE_LESS_THEN => '{attr} must be less than "{compareValueOrAttr}".',
        self::COMPARE_LESS_OR_EQUAL => '{attr} must be less than or equal to "{compareValueOrAttr}".',

        self::IN_INVALID => '{attr} is not allowed.',

        self::IP_INVALID => '{attr} must be a valid IP address.',
        self::IP_V6_NOT_ALLOWED => '{attr} must not be an IPv6 address.',
        self::IP_V4_NOT_ALLOWED => '{attr} must not be an IPv4 address.',
        self::IP_WRONG_CIDR => '{attr} contains wrong subnet mask.',
        self::IP_NO_SUBNET => '{attr} must be an IP address with specified subnet.',
        self::IP_HAS_SUBNET => '{attr} must not be a subnet.',
        self::IP_NOT_IN_RANGE => '{attr} is not in the allowed range.',

        self::UNIQUE_INVALID => '{attr} "{value}" has already been taken.',
        self::UNIQUE_COMBO_INVALID => 'The combination {values} of {attributes} has already been taken.',

        self::EXIST_INVALID => '{attr} is invalid.',

        // Custom errors
        self::CAPTCHA_INVALID => 'Wrong captcha provided.',
        self::CREDENTIALS_INVALID => 'Incorrect email address and/or password',
        self::ENTITY_BLOCKED => '{entity} is blocked',
        self::CURRENT_PASSWORD_IS_WRONG => 'Current password is wrong.',
        self::SAME_CURRENT_PASSWORD_AND_NEW_PASSWORD => 'New password can not be the same as old password',

        self::THIRD_PARTY_NOT_DOCUMENTED_ERROR => 'Not documented error.',

        self::FACEBOOK_MALFORMED_ACCESS_TOKEN => 'Malformed access token.',
        self::FACEBOOK_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',
        self::FACEBOOK_INVALID_APP_SECRET => 'Invalid appsecret_proof provided in the API argument.',
        self::FACEBOOK_ERROR_TOKEN_VALIDATION => 'Error validating access token: Session has expired.',

        self::LINKEDIN_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',

        self::GOOGLE_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',

        self::TWITTER_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',
        self::USER_NOT_FOUND => 'User not found.',
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
