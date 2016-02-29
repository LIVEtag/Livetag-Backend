<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\AccessToken;

use rest\common\models\AccessToken;
use rest\common\models\User;
use Yii;
use yii\base\Model;

/**
 * Class Create
 */
class CreateToken extends Model
{
    const YES_VALUE = 'yes';

    const NO_VALUE = 'no';

    const RANDOM_PASSWORD_LENGTH = 10;

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
    public $isVerifyIp = self::YES_VALUE;

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
            [['isRememberMe', 'isVerifyIp'], 'in', 'range' => [self::YES_VALUE, self::NO_VALUE]],
            [['userIp', 'userAgent'], 'string'],
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

        $expireTime = AccessToken::NOT_REMEMBER_ME_TIME;

        $accessToken = new AccessToken();
        $accessToken->user_id = $this->getUser()->id;

        $accessToken->token = $this->createToken($accessToken->user_id);

        $accessToken->is_verify_ip = false;
        $accessToken->is_frozen_expire = false;

        if ($this->isVerifyIp === self::YES_VALUE) {
            $accessToken->is_verify_ip = true;
        }

        if ($this->isRememberMe === self::YES_VALUE) {
            $expireTime = AccessToken::REMEMBER_ME_TIME;
            $accessToken->is_frozen_expire = true;
        }

        $accessToken->expired_at = $expireTime + time();

        return $accessToken->save() ? $accessToken : null;
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

    /**
     * @param int $userId
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function createToken($userId)
    {
        $security = Yii::$app->getSecurity();

        $hash = $security->hashData(
            $userId,
            $security->generateRandomString(self::RANDOM_PASSWORD_LENGTH)
        );
        $hash .= '_';

        return $hash . $security->generateRandomString(AccessToken::TOKEN_LENGTH - strlen($hash));
    }
}
