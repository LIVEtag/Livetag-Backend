<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\User;

use rest\common\models\User;
use rest\common\models\views\AccessToken\CreateToken;
use yii\base\Model;

/**
 * Class SignupUser
 */
class SignupUser extends Model
{
    /**
     * @var string
     */
    public $username;

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            [
                'username',
                'unique',
                'targetClass' => User::class,
                'message' => 'This username has already been taken.'
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],

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

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;

        $signupUser = $this;
        $user->on(
            User::EVENT_AFTER_INSERT,
            function () use ($user, $signupUser) {

                $accessTokenCreate = \Yii::createObject(CreateToken::class, [
                    'username' => $signupUser->username,
                    'password' => $signupUser->password,
                    'userAgent' => $signupUser->userAgent,
                    'userIp' => $signupUser->userIp,
                ]);

                $accessTokenCreate->create();
            }
        );

        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
