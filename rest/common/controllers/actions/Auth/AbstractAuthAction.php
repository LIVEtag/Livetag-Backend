<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\Auth;

use rest\components\api\actions\Action;
use yii\authclient\ClientInterface;
use yii\authclient\Collection;
use yii\authclient\InvalidResponseException;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use rest\common\models\views\User\SocialForm;

/**
 * Abstract class AbstractAuthAction
 */
abstract class AbstractAuthAction extends Action
{
    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * @param string $clientId
     * @return ClientInterface
     * @throws NotFoundHttpException
     */
    protected function getClient($clientId)
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
     * @return array
     * @throws BadRequestHttpException
     */
    protected function authOAuth2(OAuth2 $client)
    {
        $code = $this->request->post('code');

        if (!$code) {
            throw new BadRequestHttpException('Code cannot be blank.');
        }

        $authToken = $client->fetchAccessToken($code);

        $client->setAccessToken($authToken);

        try {
            return $client->getUserAttributes();
        } catch (InvalidResponseException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

}
