<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\models\views\User;

use common\models\User;
use rest\common\models\views\AccessToken\CreateToken;
use yii\base\Model;

/**
 * Class SignupUser
 */
class SignupUser extends Model
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
    public $userIp;

    /**
     * @var string
     */
    public $userAgent;

    /**
     * @var string
     */
    public $isRememberMe;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userIp'], 'string', 'max' => 46],
            [['userAgent'], 'string'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => User::class,
                'message' => 'This email address has already been taken.'
            ],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            [['isRememberMe'], 'in', 'range' => [self::YES_VALUE, self::NO_VALUE]],
            ['isRememberMe', 'default', 'value' => self::NO_VALUE],
        ];
    }

    /**
     * Signs user up
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        /** @var User $user */
        $user = $this->createUser();
        if (!$user) {
            return null;
        }

        /** @var CreateToken $accessTokenCreate */
        $accessTokenCreate = \Yii::createObject([
            'class' => CreateToken::class,
            'email' => $this->email,
            'password' => $this->password,
            'userAgent' => $this->userAgent,
            'userIp' => $this->userIp,
            'isRememberMe' => $this->isRememberMe
        ]);

        return $accessTokenCreate->create();
    }

    /**
     * @return User|null
     */
    private function createUser(): ?User
    {
        $user = new User();
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save() ? $user : null;
    }
}
