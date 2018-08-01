<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\User;

use rest\common\models\User;
use yii\base\Model;

/**
 * Class RecoveryPassword
 */
class RecoveryPassword extends Model
{
    /**
     * @var string
     */
    public $resetToken;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $confirmPassword;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['resetToken', 'password', 'confirmPassword'], 'required'],
            ['password', 'compare', 'compareAttribute' => 'confirmPassword', 'message' => 'Passwords do not match.']
        ];
    }

    /**
     * @param User $user
     * @return User
     * @throws \InvalidArgumentException
     */
    public function generateAndSendEmail(User $user)
    {
        if ($user->isNewRecord) {
            throw new \InvalidArgumentException();
        }

        $user->generatePasswordResetToken();
        if ($user->save()) {
            \Yii::$app
                ->mailer
                ->compose('recovery-password', ['user' => $user])
                ->setFrom(\Yii::$app->params['adminEmail'])
                ->setTo($user->email)
                ->send();
        }

        return $user->password_reset_token;
    }

    /**
     * Set new users's password
     *
     * @param User $user
     * @return RecoveryPassword $this
     */
    public function recovery(User $user)
    {
        if ($this->validate()) {
            $user->setPassword($this->password);
        } else {
            $this->addError('resetToken', 'Token or password is invalid.');
        }
        $user->removePasswordResetToken();
        $user->save();

        return $this;
    }
}
