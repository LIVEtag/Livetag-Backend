<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace console\controllers;

use console\models\views\User\SignupForm;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $role;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return [
            'password',
            'email',
            'role',
        ];
    }

    /**
     * Create new user
     *
     * @return int
     */
    public function actionCreate()
    {
        $user = new SignupForm();
        $user->load(
            [
                'password' => $this->password,
                'email' => $this->email,
                'role' => $this->role,
            ],
            ''
        );

        if (!$user->signup()) {
            $messages = '';
            foreach ($user->getErrors() as $attribute => $errors) {
                $messages .= $this->ansiFormat($attribute, Console::FG_GREEN)
                    . ': '
                    . $this->ansiFormat(implode(PHP_EOL, $errors) . PHP_EOL, Console::FG_RED);
            }

            echo "User creation is fail. Errors:" . PHP_EOL . $messages;

            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }
}
