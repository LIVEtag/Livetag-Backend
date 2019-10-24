<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\views\Auth;

use common\models\User\SocialProfile;
use rest\common\models\views\User\SocialForm;
use rest\common\services\Auth\AuthClientService;
use rest\components\api\exceptions\AbstractOauthException;
use rest\components\validation\ErrorList;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class TwitterAuthModel
 * @package rest\common\models\views\Auth
 */
class TwitterAuthModel extends AuthModel
{
    /**
     * @var string
     */
    public $tokenSecret;

    /**
     * TwitterAuthModel constructor.
     * @param AuthClientService $authService
     * @param string $clientType
     * @param array $config
     */
    public function __construct(AuthClientService $authService, string $clientType = self::TYPE_TWITTER, $config = [])
    {
        parent::__construct($authService, $clientType, $config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['tokenSecret'], 'required'],
            [['tokenSecret'], 'string'],
            [['token'], 'required'],
            [['token'], 'string'],
            [['token'], 'validateToken'],
        ];
    }

    /**
     * This method validates token.
     *
     * @param string $attribute
     * @throws InvalidConfigException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function validateToken(string $attribute)
    {
        $client = $this->authService->getClient('twitter');
        try {
            $this->attributes = $this->authService->authOAuth1($client, $this->token, $this->tokenSecret);
        } catch (AbstractOauthException $exception) {
            $this->addError($attribute, $exception->getErrorMessageObject(new ErrorList()));
            $this->addError('tokenSecret', $exception->getErrorMessageObject(new ErrorList()));
        }
    }

    /**
     * @return SocialForm
     */
    public function createSocialForm(): SocialForm
    {
        $socialForm = new SocialForm();

        $socialForm->email = !empty($this->attributes['email'])
            ? $this->attributes['email']
            : $this->attributes['id_str'] . '@twitter.com';
        $socialForm->socialType = SocialProfile::TYPE_TWITTER;
        $socialForm->socialId = $this->attributes['id_str'];
        $socialForm->userIp = Yii::$app->request->getUserIP();
        $socialForm->userAgent = Yii::$app->request->getUserAgent();

        return $socialForm;
    }
}
