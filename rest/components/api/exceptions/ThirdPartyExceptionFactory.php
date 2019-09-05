<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\api\exceptions;

use yii\authclient\clients\Facebook;
use yii\authclient\clients\Google;
use yii\authclient\clients\LinkedIn;
use yii\authclient\clients\Twitter;

/**
 * Class ThirdPartyExceptionFactory
 * @package rest\components\api\exceptions
 */
class ThirdPartyExceptionFactory
{
    /**
     * This method creates exception by actions $classname.
     *
     * @param string $className
     * @param string $message
     * @return AbstractOauthException|null
     */
    public static function makeException(string $className, string $message): ?AbstractOauthException
    {
        switch ($className) {
            case Facebook::class:
                return new FacebookOauthException($message);
            case LinkedIn::class:
                return new LinkedInOauthException($message);
            case Google::class:
                return new GoogleOauthException($message);
            case Twitter::class:
                return new TwitterOauthException($message);
            default:
                return null;
        }
    }
}
