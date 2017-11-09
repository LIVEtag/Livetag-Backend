<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace rest\modules\chat\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\rest\ActiveController as BaseActiveController;
use rest\components\api\AccessRule;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
use yii\filters\ContentNegotiator;

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
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), self::getDefaultBehaviors());
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultBehaviors(): array
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'user' => Yii::$app->getModule('chat')->user,
                'authMethods' => [
                    HttpBearerAuth::className(),
                ],
                'except' => [self::ACTION_OPTIONS],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'text/html' => Response::FORMAT_JSON,
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'user' => Yii::$app->getModule('chat')->user,
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action'));
                },
                'ruleConfig' => ['class' => AccessRule::className(),],
                'except' => [self::ACTION_OPTIONS],
            ],
        ];
    }
}
