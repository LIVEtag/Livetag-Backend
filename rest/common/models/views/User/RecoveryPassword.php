<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\User;

use common\models\User;
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
            ['password', 'compare', 'compareAttribute' => 'confirmPassword']
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

        return $user->passwordResetToken;
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
            $user->removePasswordResetToken();
        } else {
            $this->addError('resetToken', 'Token or password is invalid.');
        }
        $user->save();

        return $this;
    }
}
