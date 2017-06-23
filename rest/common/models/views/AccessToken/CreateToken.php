<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\AccessToken;

use rest\common\models\AccessToken;
use rest\common\models\User;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
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
    public $username;

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
            [['isRememberMe'], 'in', 'range' => [self::YES_VALUE, self::NO_VALUE]],
            [['userIp', 'userAgent'], 'string'],
            [['userIp', 'userAgent'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
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

        if ($this->user === null || !$this->user->validatePassword($this->password)) {
            $this->addError($attribute, 'User not found.');
        }
    }

    /**
     * Create user access token
     *
     * @return bool|AccessToken
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function create()
    {
        $this->user = \Yii::createObject(SearchService::class, [$this->username])->getUser();
        if ($this->user === null) {
            $this->addError('username', 'User is blocked or not found');
            return false;
        }

        if (!$this->validate()) {
            return false;
        }

        $accessToken = AccessToken::find()->findCurrentToken(
            $this->userAgent,
            $this->userIp
        )->andWhere(
            'user_id = :user_id',
            [':user_id' => $this->user->id]
        )->one();

        if ($accessToken !== null) {
            return $accessToken;
        }

        $accessToken = new AccessToken();
        $accessToken->user_id = $this->user->id;

        $expireTime = AccessToken::NOT_REMEMBER_ME_TIME;
        if ($this->isRememberMe === self::YES_VALUE) {
            $expireTime = AccessToken::REMEMBER_ME_TIME;
        }

        $accessToken->generateToken($expireTime);

        $accessToken->user_ip = $this->userIp;
        $accessToken->user_agent = $this->userAgent;

        return $accessToken->save() ? $accessToken : false;
    }
}
