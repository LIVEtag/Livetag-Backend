<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\services\Auth;

use rest\components\api\exceptions\AbstractOauthException;
use rest\components\api\exceptions\ThirdPartyExceptionFactory;
use yii\authclient\ClientInterface;
use yii\authclient\Collection;
use yii\authclient\InvalidResponseException;
use yii\authclient\OAuth1;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class AuthClientService
 * @package rest\common\services\Auth
 */
class AuthClientService
{
    /**
     * @param string $clientId
     * @return ClientInterface
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function getClient($clientId)
    {
        /** @var Collection $collection */
        $collection = \Yii::$app->get('authClientCollection');

        if (!$collection->hasClient($clientId)) {
            throw new NotFoundHttpException("Unknown auth client '{$clientId}'");
        }

        return $collection->getClient($clientId);
    }

    /**
     * @param OAuth2 $client
     * @param string $token
     * @return array
     * @throws BadRequestHttpException
     * @throws AbstractOauthException
     */
    public function authOAuth2(OAuth2 $client, string $token)
    {
        $authToken = new OAuthToken();
        $authToken->setToken($token);
        $client->setAccessToken($authToken);

        try {
            return $client->getUserAttributes();
        } catch (InvalidResponseException $exception) {
            throw $this->createThirdPartyException($exception, $client);
        }
    }

    /**
     * @param OAuth1 $client
     * @param string $token
     * @param string $tokenSecret
     * @return array
     * @throws BadRequestHttpException
     * @throws AbstractOauthException
     */
    public function authOAuth1(OAuth1 $client, string $token, string $tokenSecret)
    {
        $client->setAccessToken($this->getAuthToken($token, $tokenSecret));
        try {
            return $client->getUserAttributes();
        } catch (InvalidResponseException $exception) {
            throw $this->createThirdPartyException($exception, $client);
        }
    }

    /**
     * @param string $token
     * @param string $tokenSecret
     * @return OAuthToken
     */
    private function getAuthToken(string $token, string $tokenSecret): OAuthToken
    {
        $authToken = new OAuthToken();
        $authToken->setToken($token);
        $authToken->setTokenSecret($tokenSecret);

        return $authToken;
    }

    /**
     * @param \Exception $exception
     * @param $client
     * @return AbstractOauthException|BadRequestHttpException|null
     */
    private function createThirdPartyException(\Exception $exception, $client)
    {
        $thirdPartException = ThirdPartyExceptionFactory::makeException(
            get_class($client),
            $exception->getMessage()
        );
        if ($thirdPartException) {
            return $thirdPartException;
        } else {
            return new BadRequestHttpException($exception->getMessage());
        }
    }
}
