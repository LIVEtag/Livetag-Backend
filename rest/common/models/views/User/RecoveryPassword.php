<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\User;

use rest\common\models\User;
use rest\common\services\User\RateRequestService;
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

    public function rules()
    {
        return [
            [['resetToken', 'password', 'confirmPassword'], 'required'],
            ['password', 'compare', 'compareAttribute' => 'confirmPassword', 'message' => 'Passwords do not match.']
        ];
    }

    /**
     * Set new users's password
     * @return RecoveryPassword $this
     */
    public function recovery()
    {
        $user = User::findByPasswordResetToken($this->resetToken);
        if (!RateRequestService::rateRequest($user)) {
            $this->addError('resetToken', 'Access denied.');

            return $this;
        }
        if ($user) {
            if ($this->validate()) {
                $user->setPassword($this->password);
            } else {
                $this->addError('resetToken', 'Token is invalid.');
            }
            $user->removePasswordResetToken();
            $user->save();
        } else {
            $this->addError('resetToken', 'Token is invalid.');
        }

        return $this;
    }
}
