<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\models\forms\User;

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
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var bool
     */
    public $rememberMe = false;

    /**
     * @var User
     */
    private $user;

    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * LoginForm constructor
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
            // email and password are both required
            [['email', 'password'], 'required'],
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

        $this->user = $this->searchService->getUser($this->email);

        if ($this->user === null || !$this->user->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect e-mail or password.');
        }
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return boolean whether the user is logged in successfully
     * @throws InvalidConfigException
     * @throws InvalidParamException
     */
    public function login()
    {
        if ($this->validate()) {
            if (Yii::$app->user->login($this->user, $this->rememberMe ? 3600 * 24 * 30 : 0)) {
                Yii::$app->user->returnUrl = Yii::$app->user->identity->isAdmin ? ['shop/index'] : ['stream-session/index'];
                return true;
            }
        }
        return false;
    }
}
