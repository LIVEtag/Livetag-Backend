<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\models\forms\User;

use common\components\validation\validators\PasswordValidator;
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
            ['password', PasswordValidator::class],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Password must be equal to "New Password"']
        ];
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
