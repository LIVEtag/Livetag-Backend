<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\AccessToken;

use rest\common\models\AccessToken;
use rest\common\models\User;
use yii\base\Model;

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
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user === null || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Create user access token
     *
     * @return bool|AccessToken
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
            'user_id = :user_id',
            [':user_id' => $this->getUser()->id]
        )->one();

        if ($accessToken !== null) {
            return $accessToken;
        }

        $accessToken = new AccessToken();
        $accessToken->user_id = $this->getUser()->id;

        $expireTime = AccessToken::NOT_REMEMBER_ME_TIME;
        if ($this->isRememberMe === self::YES_VALUE) {
            $expireTime = AccessToken::REMEMBER_ME_TIME;
        }

        $accessToken->generateToken($expireTime);

        $accessToken->user_ip = $this->userIp;
        $accessToken->user_agent = $this->userAgent;

        

        return $accessToken->save() ? $accessToken : false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->user === null) {
            $this->user = User::findByUsername($this->username);
        }

        return $this->user;
    }
}
