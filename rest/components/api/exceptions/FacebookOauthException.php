<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\api\exceptions;

use common\components\validation\ErrorList;

class FacebookOauthException extends AbstractOauthException
{
    public const INVALID_APP_SECRET = 'Invalid appsecret_proof provided in the API argument';
    public const INVALID_OAUTH_TOKEN = 'Invalid OAuth access token';
    public const ERROR_TOKEN_VALIDATION = 'Error validating access token';

    /**
     * @inheritDoc
     */
    public function parseError(string $message): int
    {
        if (strpos($message, self::INVALID_OAUTH_TOKEN) !== false) {
            return ErrorList::FACEBOOK_INVALID_OAUTH_TOKEN;
        }

        if (strpos($message, self::INVALID_APP_SECRET) !== false) {
            return ErrorList::FACEBOOK_INVALID_APP_SECRET;
        }

        if (strpos($message, self::ERROR_TOKEN_VALIDATION) !== false) {
            return ErrorList::FACEBOOK_ERROR_TOKEN_VALIDATION;
        }

        return ErrorList::THIRD_PARTY_NOT_DOCUMENTED_ERROR;
    }
}
