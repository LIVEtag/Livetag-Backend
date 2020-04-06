<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\views\Auth;

use rest\common\models\views\User\SocialForm;
use rest\common\services\Auth\AuthClientService;
use rest\components\api\exceptions\AbstractOauthException;
use rest\components\validation\ErrorList;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class AuthModel
 * @package rest\common\models\views\Auth
 */
class AuthModel extends Model
{
    public const TYPE_FACEBOOK = 'facebook';
    public const TYPE_GOOGLE = 'google';
    public const TYPE_LINKEDIN = 'linkedin';
    public const TYPE_TWITTER = 'twitter';

    /**
     * @var string
     */
    public $token;

    /**
     * @var AuthClientService
     */
    protected $authService;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var string
     */
    private $clientType;

    /**
     * FacebookAuthModel constructor.
     * @param AuthClientService $authService
     * @param string $clientType
     * @param array $config
     */
    public function __construct(AuthClientService $authService, string $clientType, $config = [])
    {
        parent::__construct($config);

        $this->authService = $authService;
        $this->clientType = $clientType;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
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
        $client = $this->authService->getClient($this->clientType);

        try {
            $this->attributes = $this->authService->authOAuth2($client, $this->token);
        } catch (AbstractOauthException $exception) {
            $this->addError($attribute, $exception->getErrorMessageObject(new ErrorList()));
        }
    }

    /**
     * @return SocialForm
     */
    public function createSocialForm(): SocialForm
    {
        $socialForm = new SocialForm();
        $socialForm->email = $this->attributes['email'] ?? null;
        $socialForm->socialType = $this->clientType;
        $socialForm->socialId = $this->attributes['id'];
        $socialForm->userIp = \Yii::$app->request->getUserIP();
        $socialForm->userAgent = \Yii::$app->request->getUserAgent();

        return $socialForm;
    }
}
