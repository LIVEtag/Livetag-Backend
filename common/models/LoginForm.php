<?php
namespace common\models;

use common\components\user\SearchService;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Model;
use rest\common\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var bool
     */
    public $rememberMe = true;

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
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @throws InvalidConfigException
     */
    public function validatePassword($attribute)
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->user === null || !$this->user->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect username/e-mail or password.');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     * @throws InvalidConfigException
     * @throws InvalidParamException
     */
    public function login()
    {
        /** @var User $user */
        $this->user = \Yii::createObject(SearchService::class, [$this->username])->getUser();
        if ($this->user === null) {
            return false;
        }

        if (!$this->validate()) {
            return false;
        }

        return Yii::$app->user->login($this->user, $this->rememberMe ? 3600 * 24 * 30 : 0);
    }
}
