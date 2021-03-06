<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\components\api;

use rest\components\filters\RateLimiter;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\ActiveController as BaseActiveController;
use yii\web\Response;

class ActiveController extends BaseActiveController
{

    /**
     * default update action
     */
    const ACTION_UPDATE = 'update';

    /**
     * default index action
     */
    const ACTION_INDEX = 'index';

    /**
     * default view action
     */
    const ACTION_VIEW = 'view';

    /**
     * default create action
     */
    const ACTION_CREATE = 'create';

    /**
     * default delete action
     */
    const ACTION_DELETE = 'delete';

    /**
     * default options action
     */
    const ACTION_OPTIONS = 'options';

    /**
     * @inheritdoc
     */
    public $serializer = ['class' => Serializer::class];

    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization', 'Date'],
                    'Access-Control-Expose-Headers' => ['Date'],
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBasicAuth::class,
                    HttpBearerAuth::class,
                ],
                'except' => [self::ACTION_OPTIONS],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'text/html' => Response::FORMAT_JSON,
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_JSON,
                ],
            ],
            'rateLimiter' => [
                'class' => RateLimiter::class,
                'except' => [self::ACTION_OPTIONS],
            ],
            'access' => [
                'class' => AccessControl::class,
                'forbiddenMessage' => Yii::t('yii', 'You are not allowed to perform this action.'),
                'ruleConfig' => ['class' => AccessRule::class],
                'except' => [self::ACTION_OPTIONS],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }
}
