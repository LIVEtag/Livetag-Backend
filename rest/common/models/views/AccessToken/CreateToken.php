<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\AccessToken;

use rest\common\models\AccessToken;
use rest\common\models\User;
use rest\components\validation\ErrorList;
use rest\components\validation\ErrorListInterface;
use yii\base\InvalidConfigException;
use yii\base\Model;
use common\components\user\SearchService;

/**
 * Class Create
 */
class CreateToken extends Model
{
    const YES_VALUE = 'yes';

    const NO_VALUE = 'no';

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $isRememberMe = self::NO_VALUE;

    /**
     * @var string
     */
    public $userIp;

    /**
     * @var string
     */
    public $userAgent;

    /**
     * @var User
     */
    private $user;

    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * CreateToken constructor
     *
     * @param SearchService $searchService
     * @param array $config
     */
    public function __construct(SearchService $searchService, array $config = [])
    {
        parent::__construct($config);
        $this->searchService = $searchService;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['password', 'validatePassword'],
            [['isRememberMe'], 'in', 'range' => [self::YES_VALUE, self::NO_VALUE]],
            [['userIp', 'userAgent'], 'string'],
            [['userIp', 'userAgent'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * Validates the password
     * This method serves as the inline validation for password
     *
     * @param string $attribute the attribute currently being validated
     * @internal param array $params the additional name-value pairs given in the rule
     * @throws InvalidConfigException
     */
    public function validatePassword($attribute)
    {
        if ($this->hasErrors()) {
            return;
        }

        $this->user = $this->searchService->getUser($this->email);

        if ($this->user === null || !$this->user->validatePassword($this->password)) {
            /** @var ErrorListInterface $errorList */
            $errorList = \Yii::createObject(ErrorListInterface::class);
            $this->addError($attribute, $errorList->createErrorMessage(ErrorList::CREDENTIALS_INVALID)
                ->setParams(['email' => $this->email])
            );
        }
    }

    /**
     * Create user access token
     *
     * @return bool|AccessToken
     * @internal param $user
     */
    public function create()
    {
        if (!$this->validate()) {
            return false;
        }

        $accessToken = AccessToken::find()->findCurrentToken(
            $this->userAgent,
            $this->userIp
        )->andWhere(
            'userId = :userId',
            [':userId' => $this->user->id]
        )->one();

        if ($accessToken !== null) {
            return $accessToken;
        }

        $accessToken = new AccessToken();
        $accessToken->userId = $this->user->id;

        $expireTime = AccessToken::NOT_REMEMBER_ME_TIME;
        if ($this->isRememberMe === self::YES_VALUE) {
            $expireTime = AccessToken::REMEMBER_ME_TIME;
        }

        $accessToken->generateToken($expireTime);

        $accessToken->userIp = $this->userIp;
        $accessToken->userAgent = $this->userAgent;

        return $accessToken->save() ? $accessToken : false;
    }
}
