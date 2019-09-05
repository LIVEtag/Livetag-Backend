<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\api\exceptions;

use rest\components\validation\ErrorList;

/**
 * Class LinkedInOauthException
 * @package rest\components\api\exceptions
 */
class LinkedInOauthException extends AbstractOauthException
{
    public const INVALID_OAUTH_TOKEN = 'Invalid access token.';

    /**
     * This method parses error message and returns appropriate error code from rest\components\validation\ErrorList.
     *
     * @param string $message
     * @return int
     */
    public function parseError(string $message): int
    {
        if (strpos($message, self::INVALID_OAUTH_TOKEN) !== false) {
            return ErrorList::LINKEDIN_INVALID_OAUTH_TOKEN;
        }

        return ErrorList::THIRD_PARTY_NOT_DOCUMENTED_ERROR;
    }
}
