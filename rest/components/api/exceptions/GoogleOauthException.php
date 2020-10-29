<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\api\exceptions;

use common\components\validation\ErrorList;

/**
 * Class GoogleOauthException
 * @package rest\components\api\exceptions
 */
class GoogleOauthException extends AbstractOauthException
{
    public const INVALID_OAUTH_TOKEN = 'Expected OAuth 2 access token';

    /**
     * This method parses error message and returns appropriate error code from rest\components\validation\ErrorList.
     *
     * @param string $message
     * @return int
     */
    public function parseError(string $message): int
    {
        if (strpos($message, self::INVALID_OAUTH_TOKEN) !== false) {
            return ErrorList::GOOGLE_INVALID_OAUTH_TOKEN;
        }

        return ErrorList::THIRD_PARTY_NOT_DOCUMENTED_ERROR;
    }
}
