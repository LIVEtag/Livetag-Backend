<?php
namespace common\components\validation;

use yii\helpers\ArrayHelper;

/**
 * Class ErrorList
 * @package common\components\validation
 * @codingStandardsIgnoreFile Generic.Files.LineLength.MaxExceeded
 */
class ErrorList implements ErrorListInterface
{
    const EMAIL_INVALID = 1010;

    const DATE_INVALID = 1020;
    const DATE_TOO_SMALL = 1021;
    const DATE_TOO_BIG = 1022;

    const FILE_INVALID = 1030;
    const FILE_UPLOAD_REQUIRED = 1031;
    const FILE_TOO_MANY = 1032;
    const FILE_TOO_FEW = 1033;
    const FILE_WRONG_EXTENSION = 1034;
    const FILE_TOO_BIG = 1035;
    const FILE_TOO_SMALL = 1036;
    const FILE_WRONG_MIME_TYPE = 1037;

    const IMAGE_INVALID = 1040;
    const IMAGE_UNDER_WIDTH = 1041;
    const IMAGE_UNDER_HEIGHT = 1042;
    const IMAGE_OVER_WIDTH = 1043;
    const IMAGE_OVER_HEIGHT = 1044;

    const NUMBER_INVALID = 1050;
    const NUMBER_INTEGER_ONLY = 1051;
    const NUMBER_TOO_SMALL = 1052;
    const NUMBER_TOO_BIG = 1053;

    const REQUIRED_INVALID = 1060;
    const REQUIRED_VALUE = 1061;

    const REGULAR_EXPRESSION_INVALID = 1070;

    const STRING_INVALID = 1080;
    const STRING_TOO_SHORT = 1081;
    const STRING_TOO_LONG = 1082;
    const STRING_NOT_EQUAL = 1083;

    const URL_INVALID = 1090;

    const BOOLEAN_INVALID = 1100;

    const COMPARE_EQUAL = 1110;
    const COMPARE_NOT_EQUAL = 1111;
    const COMPARE_GREATER_THEN = 1112;
    const COMPARE_GREATER_OR_EQUAL = 1113;
    const COMPARE_LESS_THEN = 1114;
    const COMPARE_LESS_OR_EQUAL = 1115;

    const IN_INVALID = 1120;

    const IP_INVALID = 1130;
    const IP_V6_NOT_ALLOWED = 1131;
    const IP_V4_NOT_ALLOWED = 1132;
    const IP_WRONG_CIDR = 1133;
    const IP_NO_SUBNET = 1134;
    const IP_HAS_SUBNET = 1135;
    const IP_NOT_IN_RANGE = 1136;

    const UNIQUE_INVALID = 1150;
    const UNIQUE_COMBO_INVALID = 1151;

    const EXIST_INVALID = 1160;

    // Custom errors
    const CAPTCHA_INVALID = 1140;
    const CREDENTIALS_INVALID = 1200;
    const ENTITY_BLOCKED = 1210;
    const CURRENT_PASSWORD_IS_WRONG = 1220;
    const SAME_CURRENT_PASSWORD_AND_NEW_PASSWORD = 1230;
    const USER_NOT_FOUND = 1231;
    const PASSWORD_FORMAT = 1240;

    //Stream
    const STREAM_IN_PROGRESS = 1263;
    const CHECKED_TOO_MANY = 1273;

    const THIRD_PARTY_NOT_DOCUMENTED_ERROR = 3000;

    //facebook oauth error
    const FACEBOOK_INVALID_APP_SECRET = 3101;
    const FACEBOOK_ERROR_TOKEN_VALIDATION = 3102;
    const FACEBOOK_INVALID_OAUTH_TOKEN = 3103;
    const FACEBOOK_MALFORMED_ACCESS_TOKEN = 3104;

    //linkedin oauth error
    const LINKEDIN_INVALID_OAUTH_TOKEN = 3203;

    //google oauth error
    const GOOGLE_INVALID_OAUTH_TOKEN = 3303;

    //twitter oauth error
    const TWITTER_INVALID_OAUTH_TOKEN = 3403;

    protected const ERRORS = [
        self::EMAIL_INVALID => '{attribute} is not a valid email address.',

        self::DATE_INVALID => 'The format of {attribute} is invalid.',
        self::DATE_TOO_SMALL => '{attribute} must be no less than {min}.',
        self::DATE_TOO_BIG => '{attribute} must be no greater than {max}.',

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

        self::NUMBER_INVALID => '{attribute} must be a number.',
        self::NUMBER_INTEGER_ONLY => '{attribute} must be an integer.',
        self::NUMBER_TOO_SMALL => '{attribute} must be no less than {min}.',
        self::NUMBER_TOO_BIG => '{attribute} must be no greater than {max}.',

        self::REQUIRED_INVALID => '{attribute} cannot be blank.',
        self::REQUIRED_VALUE => '{attribute} must be "{requiredValue}".',

        self::REGULAR_EXPRESSION_INVALID => '{attribute} is invalid.',

        self::STRING_INVALID => '{attribute} must be a string.',
        self::STRING_TOO_SHORT => '{attribute} should contain at least {min} character(s).',
        self::STRING_TOO_LONG => '{attribute} should contain at most {max} character(s).',
        self::STRING_NOT_EQUAL => '{attribute} should contain {length} character(s).',

        self::URL_INVALID => '{attribute} is not a valid link.',

        self::BOOLEAN_INVALID => '{attribute} must be either "{true}" or "{false}"',

        self::COMPARE_EQUAL => '{attribute} must be equal to "{compareValueOrAttr}".',
        self::COMPARE_NOT_EQUAL => '{attribute} must not be equal to "{compareValueOrAttr}".',
        self::COMPARE_GREATER_THEN => '{attribute} must be greater than "{compareValueOrAttr}".',
        self::COMPARE_GREATER_OR_EQUAL => '{attribute} must be greater than or equal to "{compareValueOrAttr}".',
        self::COMPARE_LESS_THEN => '{attribute} must be less than "{compareValueOrAttr}".',
        self::COMPARE_LESS_OR_EQUAL => '{attribute} must be less than or equal to "{compareValueOrAttr}".',

        self::IN_INVALID => '{attribute} is not allowed.',

        self::IP_INVALID => '{attribute} must be a valid IP address.',
        self::IP_V6_NOT_ALLOWED => '{attribute} must not be an IPv6 address.',
        self::IP_V4_NOT_ALLOWED => '{attribute} must not be an IPv4 address.',
        self::IP_WRONG_CIDR => '{attribute} contains wrong subnet mask.',
        self::IP_NO_SUBNET => '{attribute} must be an IP address with specified subnet.',
        self::IP_HAS_SUBNET => '{attribute} must not be a subnet.',
        self::IP_NOT_IN_RANGE => '{attribute} is not in the allowed range.',

        self::UNIQUE_INVALID => '{attribute} "{value}" has already been taken.',
        self::UNIQUE_COMBO_INVALID => 'The combination {values} of {attributes} has already been taken.',

        self::EXIST_INVALID => '{attribute} is invalid.',

        // Custom errors
        self::CAPTCHA_INVALID => 'Wrong captcha provided.',
        self::CREDENTIALS_INVALID => 'Incorrect email address and/or password',
        self::ENTITY_BLOCKED => '{entity} is blocked',
        self::CURRENT_PASSWORD_IS_WRONG => 'Current password is wrong.',
        self::SAME_CURRENT_PASSWORD_AND_NEW_PASSWORD => 'New password can not be the same as old password',
        self::PASSWORD_FORMAT => 'The password should contain at least 8 symbols, one upper case, one lower case, and one number.',

        self::THIRD_PARTY_NOT_DOCUMENTED_ERROR => 'Not documented error.',

        self::FACEBOOK_MALFORMED_ACCESS_TOKEN => 'Malformed access token.',
        self::FACEBOOK_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',
        self::FACEBOOK_INVALID_APP_SECRET => 'Invalid appsecret_proof provided in the API argument.',
        self::FACEBOOK_ERROR_TOKEN_VALIDATION => 'Error validating access token: Session has expired.',

        self::LINKEDIN_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',

        self::GOOGLE_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',

        self::TWITTER_INVALID_OAUTH_TOKEN => 'Invalid OAuth access token.',
        self::USER_NOT_FOUND => 'User not found.',

        self::STREAM_IN_PROGRESS => 'Please end your existing livestrean to start a new livestream.',
        self::CHECKED_TOO_MANY => 'You can check only {number} items at once.'
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

    /**
     * @param int $code
     * @return string
     */
    public static function errorTextByCode(int $code): string
    {
        return ArrayHelper::getValue(static::ERRORS, $code, '');
    }
}
