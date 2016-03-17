<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api;

use rest\common\models\views\AccessToken\CreateToken;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController as BaseController;
use yii\web\Response;

/**
 * Class Controller
 */
class Controller extends BaseController
{
    /**
     * @inheritdoc
     */
    public $serializer = ['class' => Serializer::class];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBearerAuth::class,
                    QueryParamAuth::class,
                    [
                        'class' => HttpBasicAuth::class,
                        'auth' => function ($username, $password) {

                            $accessTokenCreate = new CreateToken();
                            $accessTokenCreate->load(
                                [
                                    'username' => $username,
                                    'password' => $password,
                                ],
                                ''
                            );

                            $accessTokenCreate->userAgent = Yii::$app->getRequest()->getUserAgent();
                            $accessTokenCreate->userIp = Yii::$app->getRequest()->getUserIP();

                            $accessToken = $accessTokenCreate->create();

                            return $accessToken->getUser()->one();
                        }
                    ],
                ],
                'except' => ['options'],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'text/html' => Response::FORMAT_JSON,
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization'],
                ],
            ],
            'access' => [
                'except' => ['options'],
            ],
        ];
    }
}
